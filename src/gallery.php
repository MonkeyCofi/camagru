<?php
    // display every uploaded photo and the username of the user who uploaded the photo
    function post(string $user, string $url) {
        return "
            <div id='post'>
                <div id='post-user-info' style='display: flex; flex-direction: row; align-items: center;'>
                    <img id='profile-picture' src='public/assets/images/ugly ass guy.jpg'>
                    <h2 id='username'>$user</h2>
                </div>
                <div style='display: flex; justify-content: flex-end'>
                    <button id='toggle-comments'>Comments -&gt;</button>
                </div>
                <div id='post-image-container'>
                    <img id='post-image' src='$url'>
                </div>
            </div>
        ";
        }
    // should fetch posts from the 
    function gallery(PDO $pdo) {
        // fetch posts from the $pdo
        $query = "SELECT users.Username, posts.PostURL, posts.CreationTime FROM users INNER JOIN posts ON users.UserId = posts.UserID";
        try {
            $pdo->beginTransaction();
            $statement = $pdo->prepare($query);
            $statement->execute();
            $posts = $statement->fetchAll(PDO::FETCH_ASSOC);
            // print_r($posts);
            $images = [];
            $ret = "";
            foreach ($posts as $post) {
                // array_push($images, '<img src=$post["PostURL"] />');
                // $ret = $ret . "<img class='post-image' src={$post["PostURL"]} />";
                $ret = $ret . post($post["Username"], $post["PostURL"]);
            }
            
            // array_push($images, "<img src='")
            
        } catch (PDOException $e) {
            $pdo->rollback();
            die("
            <h1>
                Error
            </h1><br>
            <p>" . $e . "</p>");
        }
        return $ret;
        // return "<p>Gallery</p>";
        // return post();
    }
?>