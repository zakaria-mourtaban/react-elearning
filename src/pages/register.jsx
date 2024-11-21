import React, { useState } from "react";
import axios from "axios";
import "../styles/register.css";
const Register = () => {
	const [email, setEmail] = useState("");
	const [name, setName] = useState("");
	const [password, setPassword] = useState("");
	const [error, setError] = useState("");

	const handleSubmit = (e) => {
		setError("");
		e.preventDefault();
		try {
			axios
				.post(
					"http://localhost/react-elearning/server/register.php",
					{ name, email, password },
					{
						headers: {
							"Content-Type": "application/json",
						},
					}
				)
				.then((res) => {
					console.log(res);
					if (res.data.success === "true") {
						localStorage.setItem("jwt", res.data.token);
					} else {
						setError(res.data.message);
					}
				});
			setEmail("");
			setName("");
			setPassword("");
		} catch (error) {
			setError("registration failed");
		}
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

export default Register;
