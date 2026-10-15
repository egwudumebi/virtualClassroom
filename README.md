# Virtual Classroom Web Application

A comprehensive virtual classroom platform built with Laravel and WebRTC, featuring real-time video conferencing, chat functionality, and classroom management.

## Features

### For Teachers
- Create and manage virtual classrooms
- Schedule classes with date and time
- Start/stop live video sessions
- Real-time attendance tracking
- Student approval system
- Screen sharing capabilities
- Interactive chat during sessions

### For Students
- Browse and join available classrooms
- Participate in live video sessions
- Real-time chat with teachers and peers
- Mute/unmute microphone and enable/disable video
- View class schedules and attendance

### Core Features
- **WebRTC Integration**: High-quality video and audio streaming
- **Real-time Chat**: Interactive messaging during live sessions
- **Role-based Access**: Separate interfaces for teachers and students
- **Attendance Tracking**: Automated and manual attendance logging
- **Responsive Design**: Modern UI with Bootstrap 5
- **Secure Authentication**: Laravel's built-in authentication system

## Technology Stack

- **Backend**: Laravel 12.x
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Font Awesome
- **Real-time Communication**: WebRTC
- **JavaScript**: Vanilla JS with Fetch API

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for asset compilation)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd virtual-classroom
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=virtual_classroom
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database**
   ```bash
   php artisan db:seed
   ```

8. **Compile assets**
   ```bash
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

10. **Access the application**
    Visit `http://localhost:8000` in your browser.

## Database Structure

### Tables
- `users` - User accounts with role-based access
- `roles` - User roles (teacher/student)
- `classrooms` - Virtual classroom information
- `classroom_user` - Many-to-many relationship for enrollments
- `schedules` - Class schedules and sessions
- `attendances` - Attendance tracking
- `chat_messages` - Real-time chat messages

### Key Relationships
- Teachers can create multiple classrooms
- Students can enroll in multiple classrooms
- Each classroom can have multiple schedules
- Attendance is tracked per schedule per student
- Chat messages are associated with specific schedules

## Usage Guide

### For Teachers

1. **Registration**: Register as a teacher
2. **Create Classroom**: Set up a new virtual classroom
3. **Share Class Code**: Share the generated class code with students
4. **Schedule Sessions**: Create scheduled class sessions
5. **Start Live Session**: Begin a live video conference
6. **Manage Attendance**: Track student attendance in real-time
7. **Approve Students**: Review and approve student join requests

### For Students

1. **Registration**: Register as a student
2. **Join Classroom**: Use class code to join a classroom
3. **Wait for Approval**: Wait for teacher approval
4. **View Schedules**: Check upcoming class sessions
5. **Join Live Session**: Participate in live video conferences
6. **Use Chat**: Communicate with teachers and peers

## WebRTC Configuration

The application uses WebRTC for peer-to-peer video communication. Key features:

- **STUN Servers**: Google's public STUN servers for NAT traversal
- **Media Controls**: Mute/unmute, enable/disable video
- **Screen Sharing**: Teachers can share their screen
- **Automatic Fallback**: Graceful handling of media permission denials

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Role-based Middleware**: Access control based on user roles
- **Authorization Policies**: Laravel policies for resource access
- **Input Validation**: Comprehensive form validation
- **SQL Injection Protection**: Eloquent ORM with parameter binding

## API Endpoints

### Authentication
- `POST /login` - User login
- `POST /register` - User registration
- `POST /logout` - User logout

### Teacher Routes
- `GET /teacher/dashboard` - Teacher dashboard
- `GET /teacher/classrooms` - List teacher's classrooms
- `POST /teacher/classrooms` - Create new classroom
- `GET /teacher/classrooms/{id}` - View classroom details
- `PUT /teacher/classrooms/{id}` - Update classroom
- `DELETE /teacher/classrooms/{id}` - Delete classroom

### Student Routes
- `GET /student/dashboard` - Student dashboard
- `GET /student/browse-classrooms` - Browse available classrooms
- `POST /student/join-classroom` - Join a classroom
- `GET /student/my-classrooms` - View enrolled classrooms

### Chat Routes
- `GET /chat/schedules/{id}/messages` - Get chat messages
- `POST /chat/schedules/{id}/messages` - Send message
- `DELETE /chat/messages/{id}` - Delete message

## Customization

### Styling
- Modify `resources/views/layouts/app.blade.php` for layout changes
- Update CSS in the layout file or create separate stylesheets
- Customize Bootstrap variables for theme changes

### Functionality
- Add new features by extending existing controllers
- Create new models and migrations for additional data
- Implement new middleware for custom access control

## Troubleshooting

### Common Issues

1. **Media Permissions**: Ensure browser allows camera/microphone access
2. **Database Connection**: Verify MySQL credentials in `.env`
3. **Asset Compilation**: Run `npm run dev` after making frontend changes
4. **WebRTC Issues**: Check browser compatibility and network connectivity

### Debug Mode
Enable debug mode in `.env`:
```env
APP_DEBUG=true
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please create an issue in the repository or contact the development team.

---

**Built with ❤️ using Laravel and WebRTC**
