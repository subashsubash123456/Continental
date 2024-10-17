
<footer class="bg-dark text-light text-center py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5 class="text-uppercase">About Our School</h5>
                <p>
                    Welcome to <strong>Continental School</strong>, where we strive to provide a quality education to our students. 
                    Our mission is to foster a supportive and engaging learning environment that empowers students 
                    to reach their full potential. Join us in nurturing the leaders of tomorrow!
                </p>
            </div>
            <div class="col-md-6 mb-4">
                <h5 class="text-uppercase">Developer Information</h5>
                <p>
                    Developed by <strong>Subash Poudel</strong>, a passionate web developer dedicated to creating user-friendly applications. Computer Teacher at Continental School
                    For inquiries, contact: <a href="mailto:youremail@example.com" class="text-light">mailtosubashpoudel@gmail.com</a>
                </p>
            </div>
        </div>
        <div class="social-links mb-3">
            <h5 class="text-uppercase">Follow Us</h5>
            <a href="https://www.facebook.com/your_school" target="_blank" class="btn btn-primary btn-sm mx-1">Facebook</a>
            <a href="https://www.youtube.com/channel/your_channel" target="_blank" class="btn btn-danger btn-sm mx-1">YouTube</a>
        </div>
        <div class="mt-3">
            <p>&copy; <?php echo date("Y"); ?> Continental School. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Optional: Include Bootstrap JS for better styling (if not already included in your header) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
footer {
    background-color: #343a40; /* Dark background */
}
footer h5 {
    color: #ff385c; /* School theme color for headings */
}
footer p {
    color: #e9ecef; /* Light text for readability */
}
footer .social-links a {
    text-decoration: none;
}
footer .social-links .btn {
    transition: background-color 0.3s, transform 0.3s;
}
footer .social-links .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}
</style>
