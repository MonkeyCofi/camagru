// const deleteButtons = document.getElementsByClassName('delete-post');

// Array.from(deleteButtons).forEach((button) => {
//     button.addEventListener('click', async (event) => {
//         const postId = button.dataset.id;
//         console.log(`${postId} was pressed`);
//         // const res = await fetch('/posts/')
//     });
// });

const posts = document.getElementsByClassName('post');


Array.from(posts).forEach((post) => {
    const deleteButton = post.querySelector('.delete-post');
        deleteButton.addEventListener('click', async (e) => {
            const postId = post.dataset.id;
            console.log(`${postId} was clicked`);
            try {
                const res = await fetch(`/posts/${postId}`, {
                    method: 'DELETE'
                });
                if (res.ok) {
                    // location.reload();
                    console.log("deleted post");
                }
            } catch (error) {
                console.log(error);
            }
            
        });
});