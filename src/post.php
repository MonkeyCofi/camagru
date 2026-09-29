<?php

    function delete_post(PDO $pdo, int $postId) {
        $pdo->beginTransaction();
        try {
            // delete the image file
            $query = $pdo->prepare("SELECT PostURL from posts WHERE postId = ?");
            $query->execute([$postId]);
            $res = $query->fetch(PDO::FETCH_ASSOC);
            unlink($res['PostURL']);
            $query = $pdo->prepare("DELETE FROM posts WHERE postId = ?");
            $query->execute([$postId]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("
                <h1>
                    Error
                </h1>
                <p>Could not delete post: {$e}</p>
            ");
        }
        return "Deleted post";
    }

// <div id="post">
//     <!-- <div id="post-top-bar"> -->
//         <div id="post-user-info" style="display: flex; flex-direction: row; align-items: center;">
//             <img id="profile-picture" src="public/assets/images/ugly ass guy.jpg">
//             <h2 id="username">username</h2>
//         </div>
//         <button id="toggle-comments">Comments -&gt;</button>
//     <!-- </div> -->
//     <div id="post-image-container">
//         <img id="post-image" src="public/assets/images/e93eb_Lion-Pic1.jpg">
//     </div>
// </div>