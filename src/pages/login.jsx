import React, { useState } from 'react';
import "../styles/login.css"
const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!email || !password) {
            setError('Both fields are required');
            return;
        }
		setError("");
		console.log(email);
		console.log(password);
    };

	return (
		<div className='container'>
        	<div className="login-container">
            <h1>Login</h1>
                <div className="form-group">
                    <input
                        type="email"
                        id="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        placeholder="email"
                    />
                    <input
                        type="password"
                        id="password"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="password"
                    />
                </div>
                {error && <p className="error-message">{error}</p>}
                <button onClick={handleSubmit}>Sign in</button>
			</div>
		</div>
    );
};

export default Login;
