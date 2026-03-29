<div id="login-form">
    <h2><?= $header ?></h2>
    <p>Welcome to the login page. Please enter your credentials to access your dashboard.</p>
    <form>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>