import React, { useEffect } from "react";
import { useState } from "react";
import axios from "axios";
import "../styles/courses.css";
import { useNavigate } from "react-router-dom";

const Courses = () => {
	const [courses, setCourses] = useState([]);
	const [invites, setInvites] = useState([]);
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
	const fetchinvites = () => {
		axios
			.post(
				"http://localhost/react-elearning/server/getinvites.php",
				{ jwt: localStorage.getItem("jwt") },
				{
					headers: {
						"Content-Type": "application/json",
					},
				}
			)
			.then((res) => {
				setInvites(JSON.parse(res.data.invites));
			});
	};
	const navigate = useNavigate();
	useEffect(() => {
		fetchcourses();
		fetchinvites();
		console.log(invites);
	}, []);
	return (
		<div className="courses-container">
			<div className="courses">
				<h1>available courses</h1>
				{courses.map((course) => (
					<div key={course.id} className="course">
						<div className="CourseInfo">
							<div className="icon">
								<h3>{course.name[0]}</h3>
							</div>
							<h2>{course.name}</h2>
						</div>
						<div className="ButtonsDiv">
							<button
								onClick={() => {
									axios
										.post(
											"http://localhost/react-elearning/server/enrollcourse.php",
											{
												jwt: localStorage.getItem(
													"jwt"
												),
												course_id: course.course_id,
											},
											{
												headers: {
													"Content-Type":
														"application/json",
												},
											}
										)
										.then((res) => {
											alert(res.data.message);
										});
								}}
								className="EnrollBtn"
							>
								Enroll
							</button>
							<button
								onClick={() => {
									axios
										.post(
											"http://localhost/react-elearning/server/dropcourse.php",
											{
												jwt: localStorage.getItem(
													"jwt"
												),
												course_id: course.course_id,
											},
											{
												headers: {
													"Content-Type":
														"application/json",
												},
											}
										)
										.then((res) => {
											alert(res.data.message);
										});
								}}
								className="DropBtn"
							>
								Drop
							</button>
							<button
								onClick={() => {
									localStorage.setItem("course", JSON.stringify(course))
									navigate('/course')
								}}
								className="GoToCourseBtn"
							>
								Go to Course
							</button>
						</div>
					</div>
				))}
			</div>
			<div className="invites">
				<h1>invites</h1>
				{invites.map((course) => (
					<div key={course.id} className="course">
						<div className="CourseInfo">
							<div className="icon">
								<h3>{course.name[0]}</h3>
							</div>
							<h2>{course.name}</h2>
						</div>
						<div className="ButtonsDiv">
							<button
								onClick={() => {
									axios
										.post(
											"http://localhost/react-elearning/server/acceptinvite.php",
											{
												jwt: localStorage.getItem(
													"jwt"
												),
												course_id: course.course_id,
											},
											{
												headers: {
													"Content-Type":
														"application/json",
												},
											}
										)
										.then((res) => {
											alert(res.data.message);
											fetchinvites();
										});
								}}
								className="EnrollBtn"
							>
								Accept
							</button>
							<button
								onClick={() => {
									axios
										.post(
											"http://localhost/react-elearning/server/rejectinvite.php",
											{
												jwt: localStorage.getItem(
													"jwt"
												),
												course_id: course.course_id,
											},
											{
												headers: {
													"Content-Type":
														"application/json",
												},
											}
										)
										.then((res) => {
											alert(res.data.message);
											fetchinvites();
										});
								}}
								className="DropBtn"
							>
								Reject
							</button>
						</div>
					</div>
				))}
			</div>
		</div>
	);
};

export default Courses;
