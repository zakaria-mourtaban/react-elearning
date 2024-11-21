import { BrowserRouter, Routes, Route } from "react-router-dom";
import Login from "./pages/login";
import Register from "./pages/register" 
import Courses from "./pages/courses" 
import Course from "./pages/course" 
import Assignment from "./pages/assignments" 
import Admin from "./pages/admin" 
import "@fontsource/open-sans";
import "./styles/app.css"
const App = () => {
	return (
		<div className="App">
			<BrowserRouter>
				<Routes>
					<Route path="/" element={<Login />} />
					<Route path="/register" element={<Register />} />
					<Route path="/courses" element={<Courses />} />
					<Route path="/course" element={<Course />} />
					<Route path="/assignment" element={<Assignment />} />
					<Route path="/admin" element={<Admin />} />
				</Routes>
			</BrowserRouter>
		</div>
	);
}

export default App;
