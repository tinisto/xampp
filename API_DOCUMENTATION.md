# 11klassniki.ru Mobile App API Documentation

## Overview

This document provides comprehensive documentation for the 11klassniki.ru API endpoints, designed for mobile app integration. All API endpoints follow RESTful conventions and return JSON responses.

## Base URL
```
https://11klassniki.ru/api/
```

## Authentication

Most endpoints require authentication via session cookies or API tokens.

### Login
```http
POST /api/auth/login.php
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 123,
    "first_name": "John",
    "email": "user@example.com",
    "role": "user"
  },
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### Logout
```http
POST /api/auth/logout.php
```

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

## Comments API

### Get Threaded Comments
```http
GET /api/comments/threaded.php
```

**Query Parameters:**
- `entity_type` (required): Type of entity (posts, school, vpo, spo)
- `entity_id` (required): ID of the entity
- `page` (optional): Page number (default: 1)
- `limit` (optional): Comments per page (default: 10)

**Response:**
```json
{
  "success": true,
  "comments": [
    {
      "id": 1,
      "author_of_comment": "John Doe",
      "comment_text": "Great article!",
      "date": "2025-08-09 12:00:00",
      "parent_id": null,
      "user_id": 123,
      "likes": 5,
      "dislikes": 0,
      "replies": [
        {
          "id": 2,
          "parent_id": 1,
          "author_of_comment": "Jane Smith",
          "comment_text": "I agree!",
          "date": "2025-08-09 12:30:00"
        }
      ]
    }
  ],
  "total": 42,
  "currentPage": 1,
  "totalPages": 5
}
```

### Add Comment
```http
POST /api/comments/add.php
```

**Headers:**
```
Content-Type: application/x-www-form-urlencoded
Authorization: Bearer {token} (optional)
```

**Request Body:**
```
entity_type=posts&entity_id=123&author=John+Doe&email=john@example.com&comment=This+is+a+comment&parent_id=1
```

**Response:**
```json
{
  "success": true,
  "message": "Комментарий успешно добавлен!",
  "comment": {
    "id": 123,
    "author_of_comment": "John Doe",
    "comment_text": "This is a comment",
    "date": "2025-08-09 12:00:00",
    "parent_id": 1
  }
}
```

### Like/Dislike Comment
```http
POST /api/comments/like.php
```

**Request Body:**
```json
{
  "comment_id": 123,
  "vote_type": "like" // or "dislike"
}
```

**Response:**
```json
{
  "success": true,
  "likes": 6,
  "dislikes": 1,
  "user_vote": "like"
}
```

### Edit Comment
```http
POST /api/comments/edit.php
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "comment_id": 123,
  "comment_text": "Updated comment text"
}
```

**Response:**
```json
{
  "success": true,
  "comment": {
    "id": 123,
    "comment_text": "Updated comment text",
    "edited_at": "2025-08-09 12:15:00",
    "edit_count": 1
  }
}
```

### Report Comment
```http
POST /api/comments/report.php
```

**Request Body:**
```json
{
  "comment_id": 123,
  "reason": "spam", // spam, offensive, other
  "details": "Additional details about the report"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Жалоба отправлена"
}
```

### Upload Image
```http
POST /api/comments/upload-image.php
```

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
image: [binary file data]
```

**Response:**
```json
{
  "success": true,
  "url": "/uploads/comments/comment_12345_1234567890.jpg",
  "thumbnail": "/uploads/comments/thumb_comment_12345_1234567890.jpg",
  "filename": "comment_12345_1234567890.jpg",
  "size": 245678
}
```

---

## Content API

### Get Posts
```http
GET /api/posts/list.php
```

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page (default: 20)
- `category` (optional): Filter by category
- `search` (optional): Search term

**Response:**
```json
{
  "success": true,
  "posts": [
    {
      "id": 123,
      "name": "Post Title",
      "url": "post-title",
      "description": "Post description",
      "image": "/uploads/posts/image.jpg",
      "created_at": "2025-08-09",
      "likes": 42,
      "comments_count": 15
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 10,
    "total_items": 200
  }
}
```

### Get Single Post
```http
GET /api/posts/single.php?url={post-url}
```

**Response:**
```json
{
  "success": true,
  "post": {
    "id": 123,
    "name": "Post Title",
    "url": "post-title",
    "content": "Full post content...",
    "image": "/uploads/posts/image.jpg",
    "created_at": "2025-08-09",
    "author": "Admin",
    "category": "Education",
    "tags": ["tag1", "tag2"],
    "likes": 42,
    "views": 1337
  }
}
```

---

## Educational Institutions API

### Search Schools
```http
GET /api/search-schools.php
```

**Query Parameters:**
- `query` (required): Search term
- `region` (optional): Region filter
- `type` (optional): School type filter

**Response:**
```json
{
  "success": true,
  "schools": [
    {
      "id": 123,
      "name": "School Name",
      "url": "school-url",
      "region": "Moscow",
      "city": "Moscow",
      "type": "Gymnasium",
      "rating": 4.5
    }
  ]
}
```

### Get Universities (VPO)
```http
GET /api/vpo/list.php
```

**Query Parameters:**
- `region` (optional): Region filter
- `page` (optional): Page number
- `limit` (optional): Items per page

**Response:**
```json
{
  "success": true,
  "universities": [
    {
      "id": 123,
      "short_name": "MGU",
      "full_name": "Moscow State University",
      "url": "mgu",
      "region": "Moscow",
      "website": "https://msu.ru",
      "programs_count": 150
    }
  ]
}
```

### Get Colleges (SPO)
```http
GET /api/spo/list.php
```

**Query Parameters:**
- `region` (optional): Region filter
- `specialization` (optional): Field of study
- `page` (optional): Page number

**Response:**
```json
{
  "success": true,
  "colleges": [
    {
      "id": 123,
      "short_name": "College Name",
      "url": "college-url",
      "region": "Moscow",
      "specializations": ["IT", "Economics"],
      "students_count": 500
    }
  ]
}
```

---

## User Profile API

### Get User Profile
```http
GET /api/profile/get.php?id={user_id}
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 123,
    "first_name": "John",
    "email": "john@example.com", // Only visible to self
    "occupation": "student",
    "created_at": "2025-01-01",
    "stats": {
      "total_comments": 42,
      "total_likes": 150,
      "discussions": 25
    }
  }
}
```

### Update Profile
```http
POST /api/profile/update.php
```

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "first_name": "John",
  "email": "john@example.com",
  "occupation": "teacher"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Профиль успешно обновлен"
}
```

---

## Analytics API

### Get Comment Analytics
```http
GET /api/comments/analytics.php
```

**Headers:**
```
Authorization: Bearer {token} (admin only)
```

**Query Parameters:**
- `type`: summary, timeline, sentiment, top_threads, user_activity, word_cloud
- `period`: 7d, 30d, 90d, 1y, all
- `entity_type` (optional): Filter by entity type
- `entity_id` (optional): Filter by specific entity

**Response Example (summary):**
```json
{
  "success": true,
  "type": "summary",
  "period": "30d",
  "data": {
    "total_comments": 1234,
    "unique_commenters": 456,
    "total_replies": 789,
    "avg_length": 125.5,
    "total_likes": 2345,
    "total_dislikes": 123,
    "edited_count": 45,
    "engagement_rate": 65.3,
    "reply_rate": 45.2
  }
}
```

---

## Performance Monitoring API

### Get System Metrics
```http
GET /api/comments/monitor.php
```

**Headers:**
```
Authorization: Bearer {token} (admin only)
```

**Query Parameters:**
- `type`: overview, performance, errors, trends, alerts

**Response Example (overview):**
```json
{
  "success": true,
  "type": "overview",
  "metrics": {
    "timestamp": "2025-08-09 12:00:00",
    "database": {
      "total_comments": 50000,
      "total_users": 5000,
      "comments_last_hour": 125,
      "comments_last_day": 2500
    },
    "health": {
      "database_connected": true,
      "php_version": "8.0.0",
      "memory_usage_mb": 45.2
    }
  }
}
```

---

## Error Responses

All endpoints return consistent error responses:

```json
{
  "success": false,
  "error": "Error message in Russian",
  "code": "ERROR_CODE" // Optional error code
}
```

### Common HTTP Status Codes:
- `200` - Success
- `400` - Bad Request (validation errors)
- `401` - Unauthorized
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `429` - Too Many Requests (rate limit)
- `500` - Internal Server Error

---

## Rate Limiting

The API implements dynamic rate limiting:

- **Comments**: 3 per minute, 20 per hour, 100 per day (configurable)
- **Image uploads**: 10 per hour
- **Reports**: 5 per hour
- **API calls**: 1000 per hour per IP/token

Rate limit headers:
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 950
X-RateLimit-Reset: 1628500000
```

---

## Best Practices

1. **Authentication**: Always include the Authorization header for protected endpoints
2. **Error Handling**: Check the `success` field before processing response data
3. **Pagination**: Use pagination for list endpoints to reduce load
4. **Caching**: Implement client-side caching for static content
5. **Compression**: Accept gzip encoding for faster transfers
6. **Timeouts**: Set reasonable timeouts (30s recommended)

---

## Mobile SDK Examples

### iOS (Swift)
```swift
class APIClient {
    static let baseURL = "https://11klassniki.ru/api/"
    
    func getComments(entityType: String, entityId: Int, page: Int = 1) async throws -> CommentsResponse {
        let url = URL(string: "\(APIClient.baseURL)comments/threaded.php?entity_type=\(entityType)&entity_id=\(entityId)&page=\(page)")!
        
        let (data, _) = try await URLSession.shared.data(from: url)
        return try JSONDecoder().decode(CommentsResponse.self, from: data)
    }
}
```

### Android (Kotlin)
```kotlin
class ApiService {
    companion object {
        const val BASE_URL = "https://11klassniki.ru/api/"
    }
    
    suspend fun getComments(entityType: String, entityId: Int, page: Int = 1): CommentsResponse {
        return withContext(Dispatchers.IO) {
            val response = httpClient.get("$BASE_URL/comments/threaded.php") {
                parameter("entity_type", entityType)
                parameter("entity_id", entityId)
                parameter("page", page)
            }
            response.body()
        }
    }
}
```

### React Native
```javascript
const API_BASE = 'https://11klassniki.ru/api/';

export const getComments = async (entityType, entityId, page = 1) => {
  try {
    const response = await fetch(
      `${API_BASE}comments/threaded.php?entity_type=${entityType}&entity_id=${entityId}&page=${page}`
    );
    const data = await response.json();
    
    if (!data.success) {
      throw new Error(data.error || 'Unknown error');
    }
    
    return data;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};
```

---

## Webhooks (Coming Soon)

Future support for webhooks to notify your app of events:
- New comments on followed content
- Replies to user comments
- Mentions in comments
- Content updates

---

## Support

For API support and questions:
- Email: api-support@11klassniki.ru
- Documentation: https://11klassniki.ru/api/docs
- Status Page: https://status.11klassniki.ru

---

## Changelog

### Version 1.0 (August 2025)
- Initial API release
- Comments system with full CRUD operations
- User authentication and profiles
- Content endpoints for posts, schools, universities, colleges
- Admin analytics and monitoring endpoints
- Rate limiting and security features

---

*Last updated: August 9, 2025*