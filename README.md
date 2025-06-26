# FlashcardPro

## Author
**Name**: Jéssika Dias
**Email**: jessikaldias@gmail.com

## Project Description
FlashcardPro is a Laravel + Livewire app that helps users create, organize, and study flashcards grouped in decks. It’s a simple and efficient tool for learning and memorization.

## Technologies Used
- **PHP 8.2** – Core language used for backend logic  
- **Laravel 12** – Web framework (routing, database, validation)  
- **Laravel Breeze** – Authentication scaffolding  
- **Laravel Livewire** – Reactive UI components  
- **Laravel Sail** - Docker setup
- **Tailwind CSS** – Utility-first CSS  
- **Vue.js** – Onboarding tutorial interaction  
- **SQLite** – Used in development  
- **MySQL** – Used in Docker setup  
- **Pest PHP** – Testing framework

## Setup Instructions
- **TODO**: Add detailed setup instructions here, including how to run migrations and seeders.

## ✨ Extra Features

Here are some additional features I implemented beyond the core challenge requirements:

- **Share Decks**: Users can share decks with other registered users. Shared users are allowed to study the deck but cannot edit it.
- **Onboarding Tutorial**: A step-by-step guide helps new users understand how to use the app.
- **Generate Flashcards with AI**: While creating a new deck, users can choose to generate flashcards with the help of AI, selecting the desired difficulty level for the questions.

## 🏗️ Architectural Decisions and Assumptions

### 🔐 User Authentication: Laravel Breeze
I considered building the authentication from scratch to demonstrate a deeper understanding of Laravel's authentication layer. However, I decided it would be more efficient to focus on developing the core features of the challenge, such as flashcard management, the study interface, and the API.

So I chose **Laravel Breeze** — an official, lightweight starter kit that integrates with Laravel. It provided everything I needed (registration, login, logout, route protection) with minimal setup, allowing me to move forward quickly and keep the authentication code clean and easy to maintain.


### 🗄️ Database Setup: SQLite (dev) and MySQL (Docker)

To start development quickly, I used **SQLite** for its simplicity and lack of configuration requirements.

For the final Docker-based version, I switched to **MySQL** to simulate a production-like environment and ensure compatibility with common deployment setups.


### 🧱 Working with Laravel, Livewire, and Alpine.js

Although I already had prior experience with **Laravel**, this was my first time working with **Livewire** and **Alpine.js**.

I really enjoyed working with Livewire and being able to use a single language for both the front-end and back-end. Development felt natural and moved at a good pace, which was great. Since the project had a simple scope, I didn’t run into the kinds of challenges that might come up in a large-scale application — but it was still a very valuable learning experience.

I used Alpine.js in a few specific UI controls, such as toggling the navigation menu and notifications, and I found it to be a good fit for lightweight, dynamic interfaces.


### 🧭 Onboarding Tutorial with Vue.js

As part of the challenge, I wanted to build a feature using **Vue**. Although I didn’t have prior experience with Vue in production, I’m very familiar with similar concepts from working with **Angular (2+)** and **React**, so I saw this as a good opportunity to explore the framework through this feature.

This functionality required a multi-step flow with transitions, conditional rendering, and client-side state management. It also needed to be integrated with the backend to store a flag in the database indicating whether the user had already seen the tutorial, so it wouldn’t be shown again on future logins.


### ✅ Authorization: Laravel Policies

At first, I handled access control by checking in each component whether the authenticated user had permission to view or edit a deck or flashcard. During the refinement and refactoring phase, I wanted to reduce this code duplication and make authorization more consistent throughout the app.

Initially, I considered creating a dedicated service to centralize these checks. However, I decided to implement **Laravel Policies** instead, which turned out to be a better fit — they solved the exact problem I was facing, offered a clean structure for handling permissions, and were easy to integrate in both controllers and Blade views.

This decision helped me simplify the codebase and make authorization clearer and easier to maintain.

## 🌐 Public API

The FlashcardPro application provides a REST API that allows authenticated users to manage their decks and view their own profile information. All routes are protected by an authentication middleware developed for this project.

### 🔧 Endpoints Overview

#### ✅ Health Check
- `GET /api/status`  
  Returns basic API metadata, including status, version, environment, and timestamp. Useful to confirm the application is up and running.

#### 📚 User Decks
- `GET /api/decks` – Retrieve all decks for the authenticated user  
- `POST /api/decks` – Create a new deck  
- `GET /api/decks/{deckId}` – Retrieve a specific deck by ID  
- `PUT /api/decks/{deckId}` – Update a specific deck  
- `DELETE /api/decks/{deckId}` – Delete a specific deck

#### 👤 User Profile
- `GET /api/profile`  
  Returns basic profile data for the authenticated user (ID, name, email, and account creation date).

---

### 🔐 API Authentication

To access protected API endpoints, users must authenticate using a **personal API token**.

This token is displayed on the **Profile page** of the application, under the "API Token" section. There, users can:

- View their active token
- Copy it for use in requests
- Revoke and regenerate the token if needed

Include the token in the `Authorization` header of each request using the following format:

```http
Authorization: Bearer YOUR_API_TOKEN
```

### 🛡️ Custom Middleware for API Security

To meet the challenge requirement, I implemented a **custom middleware** (`AuthenticateApiToken`) that secures API routes using token-based authentication.

It checks for a valid `Bearer` token in the `Authorization` header and attaches the corresponding user to the request if valid.

Example usage:

```php
Route::middleware(AuthenticateApiToken::class)->group(function () {
    Route::get('/decks', [DeckController::class, 'list']);
    Route::get('/profile', fn(Request $request) => response()->json([
        'id' => $request->user()->id,
        'name' => $request->user()->name,
    ]));
});
```

## 🤖 AI Tool Usage Disclosure

I used AI tools to boost productivity, explore design directions, and support development decisions.

### 🛠️ Tools Used

- **ChatGPT** – Used throughout the project to discuss implementation strategies, generate layout ideas, and refine solutions.
- **Cursor** (AI pair programming) – Used continuously during coding for brainstorming, generating code snippets, and collaboratively refactoring logic.

### 📍 When and Where

- During **UI design**, I uploaded layout references to ChatGPT and explained what I liked or wanted to change. This helped define the visual direction of the app and quickly prototype a modern and functional interface.
- During **development**, I used AI as a programming partner: I described goals and constraints, reviewed suggestions, and refined the results iteratively.

### 🎯 Why and How

My goal was to speed up repetitive or exploratory parts of the process while keeping full control over architectural decisions and code quality. AI was especially helpful for:

- Generating boilerplate code (e.g., migrations and component structure)
- Refining UI elements and transitions
- Exploring alternative implementations
- Spotting improvements in logic and readability
- Understanding Laravel best practices

I see AI as a powerful development tool, but I believe the developer must take full responsibility for reviewing, refining, and validating all generated content. AI produces code quickly, but writing clean, maintainable code still depends on thoughtful human decisions.


## 🤝 Support and Collaboration

During the development of this project, I got some occasional help from my husband, Tiago — who is also a developer at the company. Here are a few examples:

- **Layout Decision**: While creating the deck creation flow, I was unsure whether to redirect the user to another page before adding cards or to use a modal directly on the listing page. I shared both ideas with Tiago, we talked it through, and I decided to go with the modal option.

- **Route Error**: Early in the project, I ran into a frustrating 404 error on the `/deck/{deck}/edit` route. After trying several solutions without success, I asked him for help. We eventually figured out that the issue was caused by Livewire trying to bind a model even when the parameter was `null`. Renaming the parameter to `deckId` solved the problem.

- **Public API Structure**: When I started working on the API, I discussed with him how to better structure the controllers and requests. His suggestions helped make that part of the code clearer.

- **Laravel Sail**: Since it had been a while since I last worked with Docker setups, Tiago supported me and answered a few questions while I was setting up the application using Laravel Sail.

- **Generate Flashcards with AI**: Tiago helped me choose the library (Prism) and debug a schema issue with the OpenAI API. The API required the schema to start with an object instead of an array. We fixed the structure accordingly, which solved the problem.

Tiago’s help was limited to discussions and debugging support, similar to how I would collaborate with a teammate or tech lead during real-world development.