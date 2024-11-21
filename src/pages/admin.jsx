import React, { useState, useEffect } from "react";
import axios from "axios";
import "../styles/admin.css";

const Admin = () => {
    const [view, setView] = useState("dashboard");
    const [users, setUsers] = useState([]);
    const [courses, setCourses] = useState([]);
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        password: "",
        user_id: "",
        course_name: "",
        streamlink: "",
    });
    const [editMode, setEditMode] = useState(false);
    const [editingCourse, setEditingCourse] = useState(null);

    // Fetch users and courses
    const fetchData = () => {
        axios
            .post("http://localhost/react-elearning/server/admin.php", {
                jwt: localStorage.getItem("jwt"),
                action: "view_users",
            })
            .then((res) => {
                setUsers(res.data.data || []);
            });

        axios
            .post("http://localhost/react-elearning/server/getallcourses.php", {
                jwt: localStorage.getItem("jwt"),
            })
			.then((res) => {
				console.log(res.data.courses)
                setCourses(JSON.parse(res.data.courses) || []);
            });
    };

    useEffect(() => {
        fetchData();
    }, []);

    const handleCreateInstructor = () => {
        axios
            .post("http://localhost/react-elearning/server/admin.php", {
                jwt: localStorage.getItem("jwt"),
                action: "create_instructor",
                name: formData.name,
                email: formData.email,
                password: formData.password,
            })
            .then((res) => {
                alert(res.data.message);
                fetchData();
            });
    };

    const handleCreateOrEditCourse = () => {
        const action = editMode ? "create_course" : "create_course";
        const payload = {
            jwt: localStorage.getItem("jwt"),
            action,
            course_id: editMode ? editingCourse.course_id : undefined,
            name: formData.course_name,
            user_id: formData.user_id,
            streamlink: formData.streamlink,
        };

        axios.post("http://localhost/react-elearning/server/admin.php", payload).then((res) => {
            alert(res.data.message);
            setEditMode(false);
            setEditingCourse(null);
            setFormData({ course_name: "", streamlink: "", user_id: "" });
            fetchData();
        });
    };

    const handleDeleteCourse = (courseId) => {
        if (window.confirm("Are you sure you want to delete this course?")) {
            axios
                .post("http://localhost/react-elearning/server/admin.php", {
                    jwt: localStorage.getItem("jwt"),
                    action: "delete_course",
                    course_id: courseId,
                })
                .then((res) => {
                    alert(res.data.message);
                    fetchData();
                });
        }
    };

    const handleBanUser = (userId) => {
        if (window.confirm("Are you sure you want to ban this user?")) {
            axios
                .post("http://localhost/react-elearning/server/admin.php", {
                    jwt: localStorage.getItem("jwt"),
                    action: "ban_user",
                    user_id: userId,
                })
                .then((res) => {
                    alert(res.data.message);
                    fetchData();
                });
        }
    };

    return (
        <div className="admin-container">
            <aside className="sidebar">
                <button onClick={() => setView("dashboard")}>Dashboard</button>
                <button onClick={() => setView("view_users")}>View Users</button>
                <button onClick={() => setView("create_instructor")}>
                    Create Instructor
                </button>
                <button onClick={() => setView("create_course")}>
                    Create Course
                </button>
                <button onClick={() => setView("view_courses")}>View Courses</button>
            </aside>
            <main className="admin-content">
                {view === "dashboard" && (
                    <h1>Welcome to the Admin Dashboard</h1>
                )}

                {view === "view_users" && (
                    <div>
                        <h2>Users</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {users.map((user) => (
                                    <tr key={user.user_id}>
                                        <td>{user.user_id}</td>
                                        <td>{user.name}</td>
                                        <td>{user.email}</td>
                                        <td>
                                            {user.type_id === "1"
                                                ? "student"
                                                : user.type_id === "2"
                                                ? "instructor"
                                                : "admin"}
                                        </td>
                                        <td>
                                            <button
                                                onClick={() =>
                                                    handleBanUser(user.user_id)
                                                }
                                            >
                                                Ban
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}

                {view === "create_instructor" && (
                    <div>
                        <h2>Create Instructor</h2>
                        <input
                            type="text"
                            placeholder="Name"
                            value={formData.name}
                            onChange={(e) =>
                                setFormData({ ...formData, name: e.target.value })
                            }
                        />
                        <input
                            type="email"
                            placeholder="Email"
                            value={formData.email}
                            onChange={(e) =>
                                setFormData({ ...formData, email: e.target.value })
                            }
                        />
                        <input
                            type="password"
                            placeholder="Password"
                            value={formData.password}
                            onChange={(e) =>
                                setFormData({
                                    ...formData,
                                    password: e.target.value,
                                })
                            }
                        />
                        <button onClick={handleCreateInstructor}>Create</button>
                    </div>
                )}

                {view === "create_course" && (
                    <div>
                        <h2>{editMode ? "Edit Course" : "Create Course"}</h2>
                        <input
                            type="text"
                            placeholder="Course Name"
                            value={formData.course_name}
                            onChange={(e) =>
                                setFormData({
                                    ...formData,
                                    course_name: e.target.value,
                                })
                            }
                        />
                        <input
                            type="text"
                            placeholder="Streamlink"
                            value={formData.streamlink}
                            onChange={(e) =>
                                setFormData({
                                    ...formData,
                                    streamlink: e.target.value,
                                })
                            }
                        />
                        <select
                            value={formData.user_id}
                            onChange={(e) =>
                                setFormData({
                                    ...formData,
                                    user_id: e.target.value,
                                })
                            }
                        >
                            <option value="">Select Instructor</option>
                            {users
                                .filter((user) => user.type_id === "2")
                                .map((user) => (
                                    <option key={user.user_id} value={user.user_id}>
                                        {user.name}
                                    </option>
                                ))}
                        </select>
                        <button onClick={handleCreateOrEditCourse}>
                            {editMode ? "Save Changes" : "Create"}
                        </button>
                        {editMode && (
                            <button
                                onClick={() => {
                                    setEditMode(false);
                                    setEditingCourse(null);
                                    setFormData({
                                        course_name: "",
                                        streamlink: "",
                                        user_id: "",
                                    });
                                }}
                            >
                                Cancel
                            </button>
                        )}
                    </div>
                )}

                {view === "view_courses" && (
                    <div>
                        <h2>Courses</h2>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Instructor</th>
                                    <th>Streamlink</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
								{courses.map((course) => (
                                    <tr key={course.course_id}>
                                        <td>{course.course_id}</td>
                                        <td>{course.name}</td>
                                        <td>
                                            {
                                                users.find(
                                                    (u) =>
                                                        u.user_id ===
                                                        course.user_id
                                                )?.name
                                            }
                                        </td>
                                        <td>{course.streamlink}</td>
                                        <td>
                                            <button
                                                onClick={() => {
                                                    setEditMode(true);
                                                    setEditingCourse(course);
                                                    setFormData({
                                                        course_name: course.name,
                                                        streamlink:
                                                            course.streamlink,
                                                        user_id: course.user_id,
                                                    });
                                                    setView("create_course");
                                                }}
                                            >
                                                Edit
                                            </button>
                                            <button
                                                onClick={() =>
                                                    handleDeleteCourse(
                                                        course.course_id
                                                    )
                                                }
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </main>
        </div>
    );
};

export default Admin;
