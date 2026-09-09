<?php

class UserDetails {
    public $username;
    public $firstName;
    public $password;
    public $email;

    public function __construct(string $_username, string $_firstName, string $_password, string $_email) {
        $this->username = $_username;
        $this->firstName = $_firstName;
        $this->password = $_password;
        $this->email = $_email;
    }


}
// post request to register should take a 
function register_user(PDO $pdo, UserDetails $user): string {
    $query = "INSERT INTO `users` (FirstName, Email, Username, Pass) VALUES (?, ?, ?, ?)";
    try {
        $pdo->beginTransaction();
        $statement = $pdo->prepare($query);
        $statement->execute([$user->firstName, $user->email, $user->username, $user->password]);
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

function get_users(PDO $pdo) {
    $query = "SELECT UserID, Username, FirstName, Email FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
    $html = "<!DOCTYPE HTML><ul id='users' style='display: flex; flex-direction: column; border: 1px solid black; list-style-type: none;'></ul>";
    $doc = \DOM\HTMLDocument::createFromString($html);
    $list = $doc->getElementById("users"); 
    foreach($users as $user) {
        $li = $doc->createElement('li');
        $li->setAttribute('style', 'width: 20%;');
        $li->textContent = "{$user['UserID']} {$user['Username']} {$user['Email']}";
        $list->appendChild($li);
    }
    return $doc->saveHtml();
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
                <input type="submit">
            </form>
        </div>
    ';
}