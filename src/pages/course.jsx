import React, { useEffect, useState } from "react";
import "../styles/course.css";
import axios from "axios";

const Course = () => {
	const course = JSON.parse(localStorage.getItem("course"));
	const [assignments, setAssignments] = useState([]);
	const fetchassignments = () => {
		axios
			.post(
				"http://localhost/react-elearning/server/getassignments.php",
				{
					jwt: localStorage.getItem("jwt"),
					course_id: course.course_id,
				},
				{
					headers: {
						"Content-Type": "application/json",
					},
				}
			)
			.then((res) => {
				// console.log(res)
				setAssignments(JSON.parse(res.data.assignments));
			});
	};
	useEffect(() => {
		fetchassignments();
	}, []);
	return (
		<div className="content">
			<div className="Assignments">
			<h1>{course.name}</h1>
				{assignments.map((assignment) => {
					return (
						<div className="Assignment">
							<h3>{assignment.assignment}</h3>
							<h5 className="addcommenth5">add comment:</h5>
							<button className="CommentBtn">+</button>
							<h5>add attachment:</h5>
							<button className="AttachmentBtn">+</button>
						</div>
					);
				})}
			</div>
			<div className="StreamLink">
				<h2>stream:</h2>
				<a href={course.streamlink}>{course.streamlink}</a>
			</div>
		</div>
	);
};

export default Course;
