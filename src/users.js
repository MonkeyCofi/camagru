// async function deleteUser() {
(async () => {
    // const delete_buttons = document.getElementsByClassName("remove-user");
    const delete_buttons = document.querySelectorAll(".remove-user");
    if (delete_buttons) {
        Array.from(delete_buttons).forEach((button) => {
            button.addEventListener("click", async (event) => {
                const user = button.parentElement;
                console.log(user.dataset.username);
                console.log(user);
                const res = await fetch(`/users/${user.dataset.username}`, {
                    method: "DELETE"
                })
                if (res.ok) {
                    location.reload();
                }
            })
        })
    }
})();