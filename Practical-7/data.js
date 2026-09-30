const dataSources = {
  events: { file: "events.json", label: "events", filterField: "category", filterLabel: "event type" },
  students: { file: "students.json", label: "students", filterField: "program", filterLabel: "course" },
  faqs: { file: "faqs.json", label: "FAQs", filterField: "category", filterLabel: "topic" }
};

const pageSize = 6;
const recordsElement = document.getElementById("records");
const statusElement = document.getElementById("dataStatus");
const searchInput = document.getElementById("searchInput");
const filterSelect = document.getElementById("filterSelect");
const filterLabel = document.getElementById("filterLabel");
const sortSelect = document.getElementById("sortSelect");
const pageStatus = document.getElementById("pageStatus");
const previousPage = document.getElementById("previousPage");
const nextPage = document.getElementById("nextPage");
const tabs = Array.from(document.querySelectorAll(".data-tab"));

let activeType = "events";
let allRecords = [];
let currentPage = 1;
let requestNumber = 0;
let sourceNotice = "";

function getTitle(record) {
  return record.title || record.name || record.question || "";
}

function appendTextElement(parent, tagName, className, text) {
  const element = document.createElement(tagName);
  if (className) element.className = className;
  element.textContent = text;
  parent.appendChild(element);
  return element;
}

function createRecordCard(record) {
  const card = document.createElement("article");
  card.className = "record-card";

  if (activeType === "events") {
    appendTextElement(card, "h3", "", record.title);
    appendTextElement(card, "p", "record-meta", `${record.date} | ${record.time} | ${record.location}`);
    appendTextElement(card, "p", "", record.description);
    appendTextElement(card, "span", "record-tag", record.category);
  } else if (activeType === "students") {
    appendTextElement(card, "h3", "", record.name);
    appendTextElement(card, "p", "record-meta", `${record.program} | Year ${record.year}`);
    appendTextElement(card, "p", "", `Student ID: ${record.id}`);
    appendTextElement(card, "p", "", `Interests: ${record.interests}`);
    appendTextElement(card, "span", "record-tag", record.campus);
  } else {
    const details = document.createElement("details");
    const summary = document.createElement("summary");
    summary.textContent = record.question;
    details.appendChild(summary);
    appendTextElement(details, "p", "", record.answer);
    appendTextElement(details, "span", "record-tag", record.category);
    card.appendChild(details);
  }

  return card;
}

function getVisibleRecords() {
  const query = searchInput.value.trim().toLocaleLowerCase();
  const filterValue = filterSelect.value;
  const filterField = dataSources[activeType].filterField;
  const results = allRecords.filter(function (record) {
    const matchesSearch = !query || Object.values(record).join(" ").toLocaleLowerCase().includes(query);
    const matchesFilter = !filterValue || String(record[filterField]) === filterValue;
    return matchesSearch && matchesFilter;
  });

  results.sort(function (first, second) {
    if (sortSelect.value === "date-desc" && activeType === "events") {
      return new Date(second.date) - new Date(first.date);
    }
    const comparison = getTitle(first).localeCompare(getTitle(second));
    return sortSelect.value === "title-desc" ? -comparison : comparison;
  });

  return results;
}

function renderRecords() {
  const results = getVisibleRecords();
  const totalPages = Math.max(1, Math.ceil(results.length / pageSize));
  currentPage = Math.min(currentPage, totalPages);
  const startIndex = (currentPage - 1) * pageSize;
  const visibleRecords = results.slice(startIndex, startIndex + pageSize);

  recordsElement.replaceChildren(...visibleRecords.map(createRecordCard));
  recordsElement.setAttribute("aria-busy", "false");
  previousPage.disabled = currentPage <= 1;
  nextPage.disabled = currentPage >= totalPages;
  pageStatus.textContent = `Page ${currentPage} of ${totalPages}`;
  statusElement.classList.remove("is-error");

  if (results.length === 0) {
    statusElement.textContent = `${sourceNotice}No matching items. Try changing your search or filter.`;
  } else {
    statusElement.textContent = `${sourceNotice}Showing ${startIndex + 1}-${Math.min(startIndex + pageSize, results.length)} of ${results.length} ${dataSources[activeType].label}.`;
  }
}

function populateFilters() {
  const filterField = dataSources[activeType].filterField;
  const filterValues = Array.from(new Set(allRecords.map(function (record) {
    return record[filterField];
  }))).sort();

  filterSelect.replaceChildren();
  const allOption = document.createElement("option");
  allOption.value = "";
  allOption.textContent = `All ${dataSources[activeType].filterLabel}s`;
  filterSelect.appendChild(allOption);

  filterValues.forEach(function (value) {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = value;
    filterSelect.appendChild(option);
  });

  filterLabel.textContent = `Filter by ${dataSources[activeType].filterLabel}`;
  filterSelect.setAttribute("aria-label", `Filter by ${dataSources[activeType].filterLabel}`);
  searchInput.placeholder = `Search ${dataSources[activeType].label.toLocaleLowerCase()}`;

  sortSelect.replaceChildren();
  [
    ["title-asc", "Title or name A-Z"],
    ["title-desc", "Title or name Z-A"]
  ].concat(activeType === "events" ? [["date-desc", "Newest date first"]] : []).forEach(function (optionData) {
    const option = document.createElement("option");
    option.value = optionData[0];
    option.textContent = optionData[1];
    sortSelect.appendChild(option);
  });
}

function readSavedRecords(type) {
  try {
    const savedRecords = JSON.parse(localStorage.getItem(`studenthub-${type}`));
    return Array.isArray(savedRecords) ? savedRecords : null;
  } catch (error) {
    return null;
  }
}

async function loadData(type) {
  activeType = type;
  currentPage = 1;
  sourceNotice = "";
  searchInput.value = "";
  filterSelect.value = "";
  recordsElement.replaceChildren();
  recordsElement.setAttribute("aria-busy", "true");
  statusElement.classList.remove("is-error");
  statusElement.textContent = `Loading ${dataSources[type].label.toLocaleLowerCase()}...`;
  previousPage.disabled = true;
  nextPage.disabled = true;

  tabs.forEach(function (tab) {
    const selected = tab.dataset.type === type;
    tab.setAttribute("aria-selected", String(selected));
    tab.tabIndex = selected ? 0 : -1;
    tab.classList.toggle("is-active", selected);
  });
  recordsElement.setAttribute("aria-labelledby", `${type}Tab`);

  const thisRequest = ++requestNumber;
  try {
    const response = await fetch(dataSources[type].file);
    if (!response.ok) throw new Error(`Request failed with status ${response.status}`);
    const records = await response.json();
    if (!Array.isArray(records)) throw new Error("The data file must contain a JSON array.");

    if (thisRequest !== requestNumber) return;
    allRecords = records;
    try {
      localStorage.setItem(`studenthub-${type}`, JSON.stringify(records));
    } catch (error) {
      // Storage may be disabled; the fetched data still works for this visit.
    }
  } catch (error) {
    if (thisRequest !== requestNumber) return;
    const savedRecords = readSavedRecords(type);
    if (savedRecords) {
      allRecords = savedRecords;
      sourceNotice = "Could not refresh data. Showing the last saved copy. ";
    } else {
      allRecords = [];
      recordsElement.setAttribute("aria-busy", "false");
      statusElement.classList.add("is-error");
      statusElement.textContent = `Could not load ${dataSources[type].label.toLocaleLowerCase()}. Run this site from a local web server and check that ${dataSources[type].file} is available.`;
      pageStatus.textContent = "Page 1 of 1";
      return;
    }
  }

  populateFilters();
  renderRecords();
}

searchInput.addEventListener("input", function () {
  currentPage = 1;
  renderRecords();
});

filterSelect.addEventListener("change", function () {
  currentPage = 1;
  renderRecords();
});

sortSelect.addEventListener("change", function () {
  currentPage = 1;
  renderRecords();
});

previousPage.addEventListener("click", function () {
  if (currentPage > 1) {
    currentPage--;
    renderRecords();
  }
});

nextPage.addEventListener("click", function () {
  const totalPages = Math.ceil(getVisibleRecords().length / pageSize);
  if (currentPage < totalPages) {
    currentPage++;
    renderRecords();
  }
});

tabs.forEach(function (tab) {
  tab.addEventListener("click", function () {
    loadData(tab.dataset.type);
  });
  tab.addEventListener("keydown", function (event) {
    if (event.key === "ArrowRight" || event.key === "ArrowLeft") {
      event.preventDefault();
      const direction = event.key === "ArrowRight" ? 1 : -1;
      const nextIndex = (tabs.indexOf(tab) + direction + tabs.length) % tabs.length;
      tabs[nextIndex].focus();
      tabs[nextIndex].click();
    }
  });
});

loadData(activeType);
