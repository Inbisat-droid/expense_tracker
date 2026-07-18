// CSRF token for Laravel
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Get HTML elements
const form           = document.getElementById("transactionForm");
const desc           = document.getElementById("desc");
const amount         = document.getElementById("amount");
const type           = document.getElementById("type");
const category       = document.getElementById("category");
const dateInput      = document.getElementById("date");
const attachment     = document.getElementById("attachment");
const filterCategory = document.getElementById("filterCategory");

const balance = document.getElementById("balance");
const income  = document.getElementById("income");
const expense = document.getElementById("expense");
const list    = document.getElementById("list");

// Load transactions from DB (passed from Blade)
let transactions = window.initialTransactions || [];

// Render on first load
displayTransactions();
updateSummaryFromTransactions();

// ─── Add Transaction ──────────────────────────────────────────────────────────
form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Use FormData so the file is included in the request
    const formData = new FormData();
    formData.append("desc",     desc.value);
    formData.append("amount",   amount.value);
    formData.append("type",     type.value);
    formData.append("category", category.value);
    formData.append("date",     dateInput.value);

    if (attachment.files[0]) {
        formData.append("attachment", attachment.files[0]);
    }

    fetch("/transactions", {
        method: "POST",
        headers: {
            // Do NOT set Content-Type manually with FormData
            // The browser sets it automatically with the correct boundary
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json",
        },
        body: formData,
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                transactions.unshift(data.transaction); // newest first
                displayTransactions();
                updateSummary(data.summary);
                form.reset();
            }
        })
        .catch((err) => console.error("Error adding transaction:", err));
});

// ─── Display Transactions ─────────────────────────────────────────────────────
function displayTransactions() {
    list.innerHTML = "";

    const selectedCategory = filterCategory.value;

    const filtered = transactions.filter(function (item) {
        return selectedCategory === "All" || item.category === selectedCategory;
    });

    filtered.forEach(function (item) {
        const row = document.createElement("tr");

        // Build attachment cell
        let attachmentCell = "—";
        if (item.attachment) {
            // item.attachment stores the path like "attachments/filename.pdf"
            const url      = "/storage/" + item.attachment;
            const filename = item.attachment.split("/").pop();
            attachmentCell = `<a href="${url}" target="_blank" title="${filename}">📎 View</a>`;
        }

        row.innerHTML = `
            <td>${item.desc}</td>
            <td>${item.category}</td>
            <td>${item.type}</td>
            <td>₹${Number(item.amount).toFixed(2)}</td>
            <td>${item.date ? formatDate(item.date) : "—"}</td>
            <td>${attachmentCell}</td>
            <td>
                <button class="delete" onclick="deleteTransaction(${item.id})">Delete</button>
            </td>
        `;

        list.appendChild(row);
    });
}

// Format "2024-07-15" → "15 Jul 2024"
function formatDate(dateStr) {
    const d = new Date(dateStr + "T00:00:00"); // avoid timezone shift
    return d.toLocaleDateString("en-IN", { day: "2-digit", month: "short", year: "numeric" });
}

// ─── Delete One Transaction ───────────────────────────────────────────────────
function deleteTransaction(id) {
    fetch(`/transactions/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                transactions = transactions.filter((item) => item.id !== id);
                displayTransactions();
                updateSummary(data.summary);
            }
        })
        .catch((err) => console.error("Error deleting transaction:", err));
}

// ─── Delete All Transactions ──────────────────────────────────────────────────
function deleteAllTransactions() {
    if (!confirm("Delete all transactions? This cannot be undone.")) return;

    fetch("/transactions", {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Accept": "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                transactions = [];
                displayTransactions();
                updateSummary(data.summary);
            }
        })
        .catch((err) => console.error("Error deleting all:", err));
}

// ─── Summary helpers ──────────────────────────────────────────────────────────
function updateSummary(s) {
    balance.textContent = "₹" + Number(s.balance).toFixed(2);
    income.textContent  = "₹" + Number(s.income).toFixed(2);
    expense.textContent = "₹" + Number(s.expense).toFixed(2);
}

function updateSummaryFromTransactions() {
    let totalIncome = 0, totalExpense = 0;
    transactions.forEach(function (item) {
        if (item.type === "income") totalIncome  += Number(item.amount);
        else                        totalExpense += Number(item.amount);
    });
    updateSummary({ income: totalIncome, expense: totalExpense, balance: totalIncome - totalExpense });
}

// ─── Filter ───────────────────────────────────────────────────────────────────
filterCategory.addEventListener("change", displayTransactions);
