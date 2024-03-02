function toggleNav() {
    const sidebar = document.getElementById("sidebar");
    const container = document.querySelector(".container");
    const sidebarToggle = document.getElementById("sidebar-toggle");

    if (sidebar.style.width === "250px") {
        sidebar.style.width = "0";
        container.style.marginLeft = "0";
        sidebarToggle.classList.remove("opened");
    } else {
        sidebar.style.width = "250px";
        container.style.marginLeft = "250px";
        sidebarToggle.classList.add("opened");
    }
}