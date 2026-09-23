<?php

class UserDetails {
    public $username;
    public $firstName;
    public $password;
    public $email;
    public $pfp;

    public function __construct(string $_username, string $_firstName, string $_password, string $_email, string $_pfp) {
        $this->username = $_username;
        $this->firstName = $_firstName;
        $this->password = $_password;
        $this->email = $_email;
        $this->pfp = $_pfp ?? null;
    }
}

// function download_pfp(string $) {

// }
// post request to register should take a 
function register_user(PDO $pdo, UserDetails $user): string {
    // print_r($user);
    // if a pfp is provided, download it and save the filename in the Pfp column
    print_r($_POST);
    $query = "INSERT INTO `users` (FirstName, Email, Username, Pass) VALUES (?, ?, ?, ?)";
    try {
        $pdo->beginTransaction();
        $statement = $pdo->prepare($query);
        $password = password_hash($user->password, PASSWORD_BCRYPT);
        $statement->execute([$user->firstName, $user->email, $user->username, $password]);
        $pdo->commit();
    } catch (PDOException $e) {
        die("
        <h1>
            Error
        </h1><br>
        <p>" . $e . "</p>");
    }
    return "Registered user successfully";
}

function remove_user(PDO $pdo, string $username) {
    try {
        $query = $pdo->prepare("DELETE FROM users WHERE username = ?");
        $query->execute([$username]);
        $pdo->beginTransaction();
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("
            <h1>
                Error
            </h1><br>
            <p>" . "Failed to delete $username: " . $e . "</p>"
        );
    }
    return "deleted $username successfully";
}

function get_users(PDO $pdo) {
    $query = "SELECT UserID, Username, FirstName, Email FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    $html = "<ul id='users' style='display: flex; flex-direction: column; border: 1px solid black; list-style-type: none;'>";
    foreach ($users as $user) {
        $html .= "
        <li style='width: 20%;' data-id='{$user['UserID']}' data-username='{$user['Username']}'>
            {$user['UserID']} {$user["Username"]} {$user["Email"]}
            <button class='remove-user'>
                X
            </button>
        </li>";   
    }
    $html .= "
    </ul>
    <script src='users.js'></script>";
    return $html;
}

function register() {
    // use the pdo object to create the user

    return '
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <form method="post" class="input-form">
                <input type="text" name="firstname" placeholder="Enter your first name">
                <input type="email" name="email" placeholder="Enter email">
                <input type="text" name="username" placeholder="Enter username">
                <input type="password" name="password" placeholder="Enter password">
                <input id="upload-pfp" type="file" name="pfp" accept="image/jpeg image/jpg image/png">
                <label id="upload-pfp-label" for="upload-pfp">Upload profile photo</label>
                <input type="submit">
            </form>
        </div>
    ';
}