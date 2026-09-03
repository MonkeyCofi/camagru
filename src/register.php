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
function register_user(PDO $pdo, UserDetails $user): void {
    $DB_NAME = getenv("DB_NAME");
    $query = "INSERT INTO `$DB_NAME` (FirstName, Email, Username, Pass) VALUES (?, ?, ?, ?)";
    try {
        // echo $query;
        // echo
        // "<ul>
        //     <li>First name: $user->firstName</li>
        //     <li>Username: $user->username</li>
        //     <li>Email: $user->email</li>
        //     <li>Password: $user->password</li>
        // </ul>";
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
    // if it succeeds, it should return the user back to the main page but logged in
}

function get_users(PDO $pdo) {
    echo "this is called<br>";
    $query = "SELECT Username, FirstName, Email FROM users";
    $statement = $pdo->prepare($query);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    print_r(array_keys($user));
    return "
        <ul style='display: flex; flex-direction: row; border: 1px solid black; list-style-type: none;'>
            <li style='width: 20%;'>{$user['Username']}</li>
            <li style='width: 20%;'>{$user['FirstName']}</li>
            <li style='width: 20%;'>{$user['Email']}</li>
        </ul>
    ";
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