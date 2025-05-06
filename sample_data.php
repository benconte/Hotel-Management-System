<?php
require_once 'config/config.php';

// Add sample users
$users = [
    [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ],
    [
        'name' => 'Regular User',
        'email' => 'user@example.com',
        'password' => password_hash('user123', PASSWORD_DEFAULT),
        'role' => 'user'
    ]
];

foreach ($users as $user) {
    $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $user['name'], $user['email'], $user['password'], $user['role']);
    $stmt->execute();
}

// Add sample hotels
$hotels = [
    [
        'name' => 'Luxury Palace Hotel',
        'description' => 'Experience the ultimate luxury in our 5-star hotel located in the heart of the city. Our hotel offers spacious rooms with stunning views, a world-class spa, multiple restaurants serving international cuisine, and a rooftop infinity pool.',
        'location' => 'New York City, USA',
        'price_per_night' => 350.00,
        'rating' => 4.8,
        'image_url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'name' => 'Seaside Resort',
        'description' => 'Relax and unwind at our beautiful beachfront resort. Enjoy direct access to a private beach, water sports activities, spa treatments, and delicious seafood at our seaside restaurant. Perfect for family vacations or romantic getaways.',
        'location' => 'Miami, Florida, USA',
        'price_per_night' => 275.00,
        'rating' => 4.5,
        'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'name' => 'Mountain View Lodge',
        'description' => 'Escape to the mountains and enjoy breathtaking views from our cozy lodge. We offer comfortable rooms with fireplaces, hiking trails, skiing during winter months, and a restaurant serving local cuisine with organic ingredients.',
        'location' => 'Aspen, Colorado, USA',
        'price_per_night' => 225.00,
        'rating' => 4.6,
        'image_url' => 'https://images.unsplash.com/photo-1518790105602-0e2b16deeb56?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'name' => 'Urban Boutique Hotel',
        'description' => 'Stay in style at our trendy boutique hotel in the vibrant downtown area. Our uniquely designed rooms, rooftop bar with city views, and central location make it the perfect choice for exploring the city\'s attractions, restaurants, and nightlife.',
        'location' => 'San Francisco, California, USA',
        'price_per_night' => 195.00,
        'rating' => 4.3,
        'image_url' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'name' => 'Historic Charm Inn',
        'description' => 'Step back in time at our charming inn located in a beautifully restored 19th-century building. Enjoy the blend of historic architecture with modern amenities, a garden courtyard, complimentary breakfast, and attentive personal service.',
        'location' => 'Charleston, South Carolina, USA',
        'price_per_night' => 180.00,
        'rating' => 4.7,
        'image_url' => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ],
    [
        'name' => 'Tropical Paradise Resort',
        'description' => 'Experience paradise at our all-inclusive tropical resort. Enjoy luxurious accommodations, multiple swimming pools, white sandy beaches, water sports, spa services, and a variety of dining options featuring local and international cuisine.',
        'location' => 'Maui, Hawaii, USA',
        'price_per_night' => 420.00,
        'rating' => 4.9,
        'image_url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
    ]
];

foreach ($hotels as $hotel) {
    $query = "INSERT INTO hotels (name, description, location, price_per_night, rating, image_url) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssdds", $hotel['name'], $hotel['description'], $hotel['location'], 
                       $hotel['price_per_night'], $hotel['rating'], $hotel['image_url']);
    $stmt->execute();
}

echo "Sample data added successfully!";
?>