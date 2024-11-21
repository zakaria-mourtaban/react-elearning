import React, { useState } from 'react';
import { useNavigate } from "react-router-dom";
import axios from 'axios';
import "../styles/login.css"

const Login = () => {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
	const navigate = useNavigate();
    const handleSubmit = (e) => {
        e.preventDefault();
		try {
			axios
				.post(
					"http://localhost/react-elearning/server/login.php",
					{ email, password },
					{
						headers: {
							"Content-Type": "application/json",
						},
					}
				)
				.then((res) => {
					console.log(res);
					if (res.data.success === true) {
						localStorage.clear();
						localStorage.setItem("jwt", res.data.token);
						setEmail("");
						setPassword("");
						navigate("/courses")
					} else {
						setError(res.data.message);
					}
				});
		} catch (error) {
			setError("registration failed");
		}
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
