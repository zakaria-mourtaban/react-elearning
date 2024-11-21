import React, { useEffect, useState } from "react";
import "../styles/course.css";
import axios from "axios";

const Course = () => {
	const course = JSON.parse(localStorage.getItem("course"));
	const [assignments, setAssignments] = useState([]);
	const [isModalOpen, setIsModalOpen] = useState(false);
	const [modalContent, setModalContent] = useState("");
	const [comment, setComment] = useState("");
	const [isPrivate, setIsPrivate] = useState(false);
	const [selectedAssignmentId, setSelectedAssignmentId] = useState(null);
	const [attachment, setAttachment] = useState(null);

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
				setAssignments(JSON.parse(res.data.assignments));
			});
	};

	useEffect(() => {
		fetchassignments();
	}, []);

	// Function to open the modal
	const openModal = (content, assignmentId) => {
		setModalContent(content);
		setSelectedAssignmentId(assignmentId);
		setIsModalOpen(true);
	};

	// Function to close the modal
	const closeModal = () => {
		setIsModalOpen(false);
		setModalContent("");
		setComment("");
		setIsPrivate(false);
		setAttachment(null);
	};

	// Function to handle posting a comment
	const postComment = () => {
		axios
			.post(
				"http://localhost/react-elearning/server/postcomment.php",
				{
					jwt: localStorage.getItem("jwt"),
					comment: comment,
					private: isPrivate,
					assignment_id: selectedAssignmentId,
				},
				{
					headers: {
						"Content-Type": "application/json",
					},
				}
			)
			.then((res) => {
				alert("Comment posted successfully!");
				closeModal();
			})
			.catch((err) => {
				alert("Failed to post comment.");
			});
	};

	// Function to handle posting an attachment
	const postAttachment = () => {
		const formData = new FormData();
		formData.append("jwt", localStorage.getItem("jwt"));
		formData.append("assignment_id", selectedAssignmentId);
		formData.append("attachment", attachment);

		axios
			.post(
				"http://localhost/react-elearning/server/postattachment.php",
				formData,
				{
					headers: {
						"Content-Type": "multipart/form-data",
					},
				}
			)
			.then((res) => {
				alert("Attachment uploaded successfully!");
				closeModal();
			})
			.catch((err) => {
				alert("Failed to upload attachment.");
			});
	};

	return (
		<div className="content">
			<div className="Assignments">
				<h1>{course.name}</h1>
				{assignments.map((assignment) => {
					return (
						<div
							className="Assignment"
							key={assignment.assignment_id}
						>
							<h3>{assignment.assignment}</h3>
							<h5 className="addcommenth5">Add comment:</h5>
							<button
								className="CommentBtn"
								onClick={() => {
									openModal(
										"Add Comment",
										assignment.assignment_id
									);
								}}
							>
								+
							</button>
							<h5>Add attachment:</h5>
							<button
								className="AttachmentBtn"
								onClick={() =>
									openModal(
										"Add Attachment",
										assignment.assignment_id
									)
								}
							>
								+
							</button>
						</div>
					);
				})}
			</div>
			<div className="StreamLink">
				<h2>Stream:</h2>
				<a
					href={course.streamlink}
					target="_blank"
					rel="noopener noreferrer"
				>
					{course.streamlink}
				</a>
			</div>

			{/* Modal */}
			{isModalOpen && (
				<div className="modal">
					<div className="modal-content">
						<h2>{modalContent}</h2>

						{modalContent === "Add Comment" && (
							<div>
								<textarea
									value={comment}
									onChange={(e) => setComment(e.target.value)}
									placeholder="Write your comment here"
									rows="4"
									cols="50"
								></textarea>
								<br />
								<label>
									Private:
									<input
										type="checkbox"
										checked={isPrivate}
										onChange={(e) =>
											setIsPrivate(e.target.checked)
										}
									/>
								</label>
								<br />
								<button onClick={postComment}>
									Post Comment
								</button>
							</div>
						)}

						{modalContent === "Add Attachment" && (
							<div>
								<input
									type="file"
									onChange={(e) =>
										setAttachment(e.target.files[0])
									}
								/>
								<br />
								<button onClick={postAttachment}>
									Upload Attachment
								</button>
							</div>
						)}

						<button onClick={closeModal}>Close</button>
					</div>
				</div>
			)}
		</div>
	);
};

export default Course;
