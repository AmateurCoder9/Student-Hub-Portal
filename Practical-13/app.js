
const API_URL = "api/events_api.php";

const eventsList = document.getElementById("eventsList");
const searchInput = document.getElementById("searchInput");
const loadingText = document.getElementById("loadingText");
const msgBox = document.getElementById("msgBox");
const formCard = document.getElementById("eventFormCard");
const showFormBtn = document.getElementById("showFormBtn");
const cancelFormBtn = document.getElementById("cancelFormBtn");
const addEventForm = document.getElementById("addEventForm");

let debounceTimer;

function showMessage(msg, isSuccess = true) {
    msgBox.textContent = msg;
    msgBox.className = "alert " + (isSuccess ? "success" : "error");
    msgBox.style.display = "block";
    setTimeout(() => {
        msgBox.style.display = "none";
    }, 3000);
}

async function loadEvents(query = "") {
    loadingText.style.display = "block";
    try {
        const url = query ? `${API_URL}?q=${encodeURIComponent(query)}` : API_URL;
        const res = await fetch(url);
        const data = await res.json();
        
        loadingText.style.display = "none";
        renderEvents(data.data || []);
    } catch (err) {
        loadingText.textContent = "Error loading events from server.";
    }
}

function renderEvents(events) {
    eventsList.innerHTML = "";
    if (events.length === 0) {
        eventsList.innerHTML = "<p style='color: #888;'>No events found.</p>";
        return;
    }

    events.forEach(ev => {
        const card = document.createElement("div");
        card.className = "event-card";
        card.innerHTML = `
            <h3>${ev.title}</h3>
            <p style="color: #666; font-size: 13px;">Category: <strong>${ev.category}</strong> | Date: ${ev.event_date}</p>
            <p>${ev.description}</p>
            <button onclick="deleteEvent(${ev.id})" style="background: #dc2626; color: white; padding: 6px 12px; font-size: 13px; width: auto; border: none; border-radius: 4px; cursor: pointer;">Delete</button>
        `;
        eventsList.appendChild(card);
    });
}

searchInput.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        loadEvents(searchInput.value.trim());
    }, 300);
});

showFormBtn.addEventListener("click", () => {
    formCard.style.display = "block";
});
cancelFormBtn.addEventListener("click", () => {
    formCard.style.display = "none";
});

addEventForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
        title: document.getElementById("title").value.trim(),
        category: document.getElementById("category").value,
        event_date: document.getElementById("event_date").value,
        description: document.getElementById("description").value.trim()
    };

    try {
        const res = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await res.json();
        if (result.status === "success") {
            showMessage(result.message);
            addEventForm.reset();
            formCard.style.display = "none";
            loadEvents(); 
        } else {
            showMessage(result.message, false);
        }
    } catch (err) {
        showMessage("Failed to create event.", false);
    }
});

window.deleteEvent = async function(id) {
    if (!confirm("Are you sure you want to delete this event?")) return;

    try {
        const res = await fetch(`${API_URL}?id=${id}`, { method: "DELETE" });
        const result = await res.json();
        if (result.status === "success") {
            showMessage("Event deleted successfully!");
            loadEvents(searchInput.value.trim());
        } else {
            showMessage(result.message, false);
        }
    } catch (err) {
        showMessage("Error deleting event.", false);
    }
};

loadEvents();
