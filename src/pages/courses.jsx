import React, { useEffect } from "react";
import { useState } from "react";
import axios from "axios";
import "../styles/courses.css"

const Courses = () => {
	const [courses, setCourses] = useState([]);

	const fetchcourses = () => {
		axios
			.post(
				"http://localhost/react-elearning/server/getallcourses.php",
				{ jwt: localStorage.getItem("jwt") },
				{
					headers: {
						"Content-Type": "application/json",
					},
				}
			)
			.then((res) => {
				setCourses(JSON.parse(res.data.courses));
			});
	};
	useEffect(() => {
		fetchcourses();
		console.log(courses);
	}, []);
	return (
		<div className="courses-container">
			<h1>available courses</h1>
			<div className="courses">
				{courses.map((course) => (
					<div key={course.id}>{course.name}</div>
				))}
			</div>
		</div>
	);
};

export default Courses;
