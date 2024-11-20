import React, { useState } from "react";
import "../styles/register.css";
const Login = () => {
	const [email, setEmail] = useState("");
	const [name, setName] = useState("");
	const [password, setPassword] = useState("");
	const [error, setError] = useState("");

	const handleSubmit = (e) => {
		e.preventDefault();
		if (!email || !password) {
			setError("Both fields are required");
			return;
		}
		setError("");
		console.log(email);
		console.log(password);
	};

	return (
		<div className="regcontainer">
			<div className="register-container">
				<h1>Register</h1>
				<div className="regform-group">
					<input
						type="name"
						id="name"
						value={name}
						onChange={(e) => setName(e.target.value)}
						placeholder="name"
					/>{" "}
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
				<button onClick={handleSubmit}>Sign up</button>
			</div>
		</div>
	);
};

export default Login;
