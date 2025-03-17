# Gestion des Candidats REST API - Authentification

Ce projet est une API REST pour l'authentification des utilisateurs avec Laravel Sanctum.

## 1. Inscription (`POST /api/register`)

**URL:** http://127.0.0.1:8000/api/register

**Description :**  
Ce endpoint permet de créer un compte utilisateur et de générer un token d'authentification.

###  Exemple de requête (JSON) :
```json
{
  "name": "Mohsen Elrhazi",
  "email": "mohsen@example.com",
  "password": "123456",
  "password_confirmation": "123456"
}

`Réponse (201 Created) : 

{
  "status": "success",
  "message": "Utilisateur enregistré avec succès",
  "data": {
    "name": "Mohsen Elrhazi",
    "email": "mohsen@example.com",
    "token": "xxxxxxxxxxxxxxxxxx"
  }
} 

# Connexion (`POST /api/login`)

**URL:** `http://127.0.0.1:8000/api/login`

## 📌 Description  
Ce endpoint permet à un utilisateur de se connecter .

## 📤 Exemple de requête (JSON)  
```json
{
  "email": "mohsen@example.com",
  "password": "123456"
}

Réponse en cas de succès (200 OK)
json

{
    "status": "success",
    "message": "Authentification réussi",
    "data": {
        "id": 1,
        "name": "mohsen",
        "email": "mohsen@gmail.com",
        "email_verified_at": null,
        "created_at": "2025-03-10T22:54:14.000000Z",
        "updated_at": "2025-03-10T22:54:14.000000Z"
    },
    "token": "7|bUt1DJCKGu72aaRi3rCjBwTBeLcwICLuGInnNA06caa74a1a"
}

Réponse en cas d'erreur (401 Unauthorized)
json

{
    "status": "error",
    "message": "Échec de l\"authentification, vérifiez vos informations",
    "data": null
}

