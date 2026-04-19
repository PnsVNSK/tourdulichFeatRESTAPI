<?php
/**
 * Test file to verify wishlist database table and connection
 * Access this file via: http://localhost/tour1/test_wishlist_db.php
 */

// Start output buffering
ob_start();

echo "<h1>Wishlist Database Test</h1>";
echo "<hr>";

// Load environment variables
$dotenv_path = __DIR__ . '/.env';
if (file_exists($dotenv_path)) {
    $lines = file($dotenv_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Database credentials
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPass = getenv('DB_PASS') ?: '';
$dbName = getenv('DB_NAME') ?: 'tour';

echo "<h2>1. Database Connection Info</h2>";
echo "<pre>";
echo "Host: $dbHost\n";
echo "User: $dbUser\n";
echo "Database: $dbName\n";
echo "</pre>";

try {
    // Connect to database
    $dbh = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8mb4'",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
        )
    );
    
    echo "<h2>2. Database Connection: <span style='color: green;'>✓ SUCCESS</span></h2>";
    
    // Check if tblwishlist table exists
    echo "<h2>3. Check tblwishlist Table</h2>";
    $query = $dbh->query("SHOW TABLES LIKE 'tblwishlist'");
    $tableExists = $query->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ Table 'tblwishlist' exists</p>";
        
        // Get table structure
        echo "<h3>Table Structure:</h3>";
        echo "<pre>";
        $query = $dbh->query("DESCRIBE tblwishlist");
        $columns = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo sprintf("%-15s %-20s %-10s %-10s %-20s\n", 
                $col['Field'], 
                $col['Type'], 
                $col['Null'], 
                $col['Key'],
                $col['Extra']
            );
        }
        echo "</pre>";
        
        // Get indexes
        echo "<h3>Table Indexes:</h3>";
        echo "<pre>";
        $query = $dbh->query("SHOW INDEX FROM tblwishlist");
        $indexes = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($indexes as $idx) {
            echo sprintf("Key: %-20s Column: %-15s Unique: %s\n", 
                $idx['Key_name'], 
                $idx['Column_name'],
                $idx['Non_unique'] == 0 ? 'Yes' : 'No'
            );
        }
        echo "</pre>";
        
        // Count records
        echo "<h3>Record Count:</h3>";
        $query = $dbh->query("SELECT COUNT(*) as count FROM tblwishlist");
        $result = $query->fetch(PDO::FETCH_OBJ);
        echo "<p>Total records: <strong>{$result->count}</strong></p>";
        
        // Show sample records if any
        if ($result->count > 0) {
            echo "<h3>Sample Records (first 5):</h3>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>UserEmail</th><th>PackageId</th><th>CreatedAt</th></tr>";
            $query = $dbh->query("SELECT * FROM tblwishlist LIMIT 5");
            while ($row = $query->fetch(PDO::FETCH_OBJ)) {
                echo "<tr>";
                echo "<td>{$row->id}</td>";
                echo "<td>{$row->UserEmail}</td>";
                echo "<td>{$row->PackageId}</td>";
                echo "<td>{$row->CreatedAt}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Table 'tblwishlist' does NOT exist!</p>";
        echo "<p>Please run the database.sql script to create the table.</p>";
    }
    
    // Check if tbltourpackages exists
    echo "<h2>4. Check tbltourpackages Table</h2>";
    $query = $dbh->query("SHOW TABLES LIKE 'tbltourpackages'");
    $packageTableExists = $query->rowCount() > 0;
    
    if ($packageTableExists) {
        echo "<p style='color: green;'>✓ Table 'tbltourpackages' exists</p>";
        $query = $dbh->query("SELECT COUNT(*) as count FROM tbltourpackages");
        $result = $query->fetch(PDO::FETCH_OBJ);
        echo "<p>Total packages: <strong>{$result->count}</strong></p>";
    } else {
        echo "<p style='color: red;'>✗ Table 'tbltourpackages' does NOT exist!</p>";
    }
    
    // Check if tblusers exists
    echo "<h2>5. Check tblusers Table</h2>";
    $query = $dbh->query("SHOW TABLES LIKE 'tblusers'");
    $usersTableExists = $query->rowCount() > 0;
    
    if ($usersTableExists) {
        echo "<p style='color: green;'>✓ Table 'tblusers' exists</p>";
        $query = $dbh->query("SELECT COUNT(*) as count FROM tblusers");
        $result = $query->fetch(PDO::FETCH_OBJ);
        echo "<p>Total users: <strong>{$result->count}</strong></p>";
    } else {
        echo "<p style='color: red;'>✗ Table 'tblusers' does NOT exist!</p>";
    }
    
    echo "<h2>6. Test Query Result</h2>";
    if ($tableExists && $packageTableExists) {
        echo "<p style='color: green;'>✓ All required tables exist. The wishlist feature should work correctly.</p>";
        echo "<h3>Recommendation:</h3>";
        echo "<ul>";
        echo "<li>Clear your browser cache and cookies</li>";
        echo "<li>Try adding a tour to wishlist from the tour details page</li>";
        echo "<li>Check browser console (F12) for any JavaScript errors</li>";
        echo "<li>Check XAMPP error logs if issues persist</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: red;'>✗ Some tables are missing. Please run the database.sql script.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database Connection: <span style='color: red;'>✗ FAILED</span></h2>";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Common Solutions:</h3>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL is running</li>";
    echo "<li>Check if database '$dbName' exists in phpMyAdmin</li>";
    echo "<li>Verify database credentials in .env file</li>";
    echo "<li>Run the database.sql script in phpMyAdmin</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
echo "<p><a href='index.php'>← Back to Home</a></p>";

// Flush output
ob_end_flush();
?>
