<div id="sidebar">
    <button>Gallery</button>
    <button>Upload</button>
    <button>Settings</button>
</div>
<nav id="navbar">
    <button id="sidebar-btn" style="display: flex; justify-content: center; align-items: center;">
        <img id="hamburger" src="./public/assets/hamburger.png">
    </button>
    <h2>Logo</h2>
    <span></span>
</nav>

<script>
    const sidebar_btn = document.getElementById("sidebar-btn").children[0];
    const sidebar = document.getElementById("sidebar");
    const pages = sidebar.children;
    let sidebar_visibility = "hidden";
    sidebar_btn.addEventListener("click", () => {
        sidebar_visibility = "visible";
        sidebar.style.visibility = sidebar_visibility;
    });
    document.addEventListener("click", (event) => {
        if (sidebar_visibility === "visible") {
            if (event.target !== sidebar && event.target !== sidebar_btn && !(Array.from(pages).includes(event.target))) {
                if ((event.target in pages))
                    console.log("option pressed");
                console.log("inside here");
                sidebar_visibility = "hidden";
                sidebar.style.visibility = sidebar_visibility;
            }
        }
    })
</script>
<!-- <a href="https://www.flaticon.com/free-icons/hamburger" title="hamburger icons">Hamburger icons created by feen - Flaticon</a> -->