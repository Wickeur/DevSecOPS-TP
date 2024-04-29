<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Fonction pour générer un nom aléatoire
function randomName() {
    $names = ['John', 'Jane', 'Alice', 'Bob', 'Eve', 'Michael', 'Emily', 'David', 'Sarah', 'Daniel'];
    return $names[array_rand($names)];
}

// Fonction pour générer un numéro de téléphone aléatoire
function randomPhone() {
    return '0' . rand(100000000, 999999999); // Numéro de téléphone français généré aléatoirement
}

// Fonction pour générer une adresse e-mail aléatoire
function randomEmail() {
    $domains = ['example.com', 'test.com', 'domain.com', 'mail.com', 'fake.com'];
    return strtolower(randomName() . rand(1, 100) . '@' . $domains[array_rand($domains)]);
}

// Fonction pour générer de faux contacts
function generateFakeContacts($count) {
    $contacts = [];
    for ($i = 0; $i < $count; $i++) {
        $contacts[] = [
            'name' => randomName(),
            'phone' => randomPhone(),
            'email' => randomEmail()
        ];
    }
    return $contacts;
}

// Génération de 10 faux contacts
$contacts = generateFakeContacts(10);

echo json_encode($contacts);

// Fonction pour récupérer la liste des contacts
function getContacts() {
    global $contacts;
    return $contacts;
}

// Fonction pour récupérer le détail d'un contact par son ID
function getContactById($id) {
    global $contacts;
    foreach ($contacts as $contact) {
        if ($contact['id'] == $id) {
            return $contact;
        }
    }
    return null; // Retourne null si le contact n'est pas trouvé
}

// Fonction pour ajouter un nouveau contact
function addContact($name, $phone, $email) {
    global $contacts;
    $id = uniqid(); // Génération d'un ID unique pour le contact
    $contacts[] = ['id' => $id, 'name' => $name, 'phone' => $phone, 'email' => $email];
    return $id; // Retourne l'ID du nouveau contact
}

// Fonction pour modifier un contact existant
function updateContact($id, $name, $phone, $email) {
    global $contacts;
    foreach ($contacts as &$contact) {
        if ($contact['id'] == $id) {
            $contact['name'] = $name;
            $contact['phone'] = $phone;
            $contact['email'] = $email;
            return true; // Retourne true si la mise à jour est réussie
        }
    }
    return false; // Retourne false si le contact n'est pas trouvé
}

// Fonction pour supprimer un contact existant
function deleteContact($id) {
    global $contacts;
    foreach ($contacts as $key => $contact) {
        if ($contact['id'] == $id) {
            unset($contacts[$key]);
            return true; // Retourne true si la suppression est réussie
        }
    }
    return false; // Retourne false si le contact n'est pas trouvé
}

// Routeur pour gérer les requêtes HTTP
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupération de la liste des contacts
    if ($_SERVER['REQUEST_URI'] === '/contacts') {
        echo json_encode(getContacts());
    }
    // Récupération du détail d'un contact par son ID
    elseif (preg_match('/\/contacts\/(\w+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $contact = getContactById($matches[1]);
        if ($contact) {
            echo json_encode($contact);
        } else {
            http_response_code(404);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'un nouveau contact
    if ($_SERVER['REQUEST_URI'] === '/contacts') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = addContact($data['name'], $data['phone'], $data['email']);
        echo json_encode(['id' => $id]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Modification d'un contact existant
    if (preg_match('/\/contacts\/(\w+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $data = json_decode(file_get_contents('php://input'), true);
        $success = updateContact($matches[1], $data['name'], $data['phone'], $data['email']);
        if ($success) {
            echo json_encode(['message' => 'Contact updated successfully']);
        } else {
            http_response_code(404);
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Suppression d'un contact existant
    if (preg_match('/\/contacts\/(\w+)/', $_SERVER['REQUEST_URI'], $matches)) {
        $success = deleteContact($matches[1]);
        if ($success) {
            echo json_encode(['message' => 'Contact deleted successfully']);
        } else {
            http_response_code(404);
        }
    }
}

?>
