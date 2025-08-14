# 📚 Blog Platform API

A **Laravel-based RESTful API** for a blogging platform where **admins** can manage content (posts, categories, users) and **users** can interact with posts through likes, comments, replies, and shares.  
Includes role-based authentication, profile image management, and post image uploads.

---

## 🚀 Features

* User Authentication

* Role-based Access Control 

* Post Management (CRUD)

* Comment System with nested replies

* Like/Unlike Posts and Comments

* Category Management

* Post Image Uploads

* Profile Image Uploads

* Admin & User Separation for actions

### **Authentication**
- User registration and login with **Laravel Sanctum** authentication.
- Logout and get current user information.
- Role-based middleware (`CheckGuard`) for Admin/User access control.

### **Posts**
- Create, update, view, and delete blog posts.
- Like and unlike posts.
- View all liked posts by authenticated user.
- Get comments and like count for a post.

### **Comments**
- Add comments to posts and reply to comments.
- Like and unlike comments.
- View liked comments by authenticated user.
- Delete and update comments.
- View replies to a comment.

### **Categories**
- Public endpoints to browse categories.
- Admin endpoints to create, update, and delete categories.

### **Users**
- View all users or a specific user.
- Update and delete a user account.

### **Media**
- **Post Images**: Upload, update, and view post images.
- **Profile Images**: Upload, view current user profile image, and view all profile images.

---

## API Endpoints

> Base URL: `/api`  
> All routes requiring authentication use `auth:sanctum` middleware.

### **Authentication**
| Method | Endpoint       | Description |
|--------|----------------|-------------|
| POST   | `/register`    | Register a new user |
| POST   | `/login`       | Login and receive token |
| POST   | `/logout`      | Logout user *(auth required)* |
| GET    | `/userInfo`    | Get authenticated user info |

---

### **Categories**
**Public**:
| Method | Endpoint           | Description |
|--------|--------------------|-------------|
| GET    | `/categories`      | List all categories |
| GET    | `/categories/{id}` | View category details |

**Admin** *(auth required)*:
| Method | Endpoint             | Description |
|--------|----------------------|-------------|
| POST   | `/categories`        | Create category |
| PUT    | `/categories/{id}`   | Update category |
| DELETE | `/categories/{id}`   | Delete category |

---

### **Posts** *(auth required)*
| Method | Endpoint                     | Description |
|--------|------------------------------|-------------|
| GET    | `/posts`                     | List all posts |
| GET    | `/posts/{id}`                 | View post details |
| POST   | `/posts`                      | Create new post |
| PUT    | `/posts/{id}`                  | Update post |
| DELETE | `/posts/{id}`                  | Delete post |
| POST   | `/posts/{post}/like`           | Like a post |
| POST   | `/posts/{post}/unlike`         | Unlike a post |
| GET    | `/posts/liked`                 | View liked posts |
| GET    | `/posts/{post_id}/comments`    | View comments for a post |
| GET    | `/posts/{post}/likes`          | View post like count |

---

### **Comments** *(auth required)*
| Method | Endpoint                         | Description |
|--------|----------------------------------|-------------|
| GET    | `/comments`                      | List all comments |
| GET    | `/comments/{id}`                 | View comment details |
| POST   | `/comments`                      | Create comment |
| PUT    | `/comments/{id}`                 | Update comment |
| DELETE | `/comments/{comment}`            | Delete comment |
| GET    | `/comments/liked`                | View liked comments |
| GET    | `/comments/user`                 | View comments by current user |
| GET    | `/comments/{id}/replies`         | View replies to comment |
| GET    | `/comments/{comment}/likes`      | View comment like count |
| POST   | `/comments/{comment}/like`       | Like a comment |
| POST   | `/comments/{comment}/unlike`     | Unlike a comment |

---

### **Users** *(auth required)*
| Method | Endpoint           | Description |
|--------|--------------------|-------------|
| GET    | `/users`           | List all users |
| GET    | `/users/{user}`    | View user profile |
| PATCH  | `/users/{user}`    | Update user |
| DELETE | `/users/{user}`    | Delete user |

---

### **Post Images** *(auth required)*
| Method | Endpoint                | Description |
|--------|-------------------------|-------------|
| GET    | `/post_images`          | List post images |
| GET    | `/post_images/{id}`     | View specific post image |
| POST   | `/post_images`          | Upload post image |
| PUT    | `/post_images/{id}`     | Update post image |

---

### **Profile Images** *(auth required)*
| Method | Endpoint                       | Description |
|--------|--------------------------------|-------------|
| POST   | `/profile_images/upload`       | Upload current user's profile image |
| GET    | `/profile_images`              | List all profile images |
| GET    | `/profile_images/current`      | Get current user's profile image |

---

## Installation & Setup

1. **Clone the repository**
```bash
git clone https://github.com/Yahyalimouni/blog-api.git
cd blog-api
```

2. **Install dependencies**
```bash
    composer install
```

3. **Copy environment file**
```bash
    cp .env.example .env
```

* Configure your database, storage, and authentication settings.

4. **Generate app key**
```bash
    php artisan key:generate
```

5. **Run migrations**
```bash
    php artisan migrate
```
6. **Serve the application**
```bash
    php artisan serve
```

`The API will be available at:`
http://127.0.0.1:8000/api
