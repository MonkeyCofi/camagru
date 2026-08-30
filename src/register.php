<?php

function register() {
    return '
        <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <form method="post" class="input-form">
                <input type="email" name="email" placeholder="Enter email">
                <input type="text" name="username" placeholder="Enter username">
                <input type="password" name="password" placeholder="Enter password">
                <input type="submit">
            </form>
        </div>
    ';
}