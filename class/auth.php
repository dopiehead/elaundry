<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../class/config.php';

class Auth
{
    private \mysqli $conn;
    private Cloudinary $cloudinary;

    public function __construct(private readonly Database $db)
    {
        $this->conn = $db->conn;
    }

    public function getConnection(): \mysqli
    {
        return $this->conn;
    }

    /**
     * Register a new user (with Cloudinary upload support)
     */
    public function register(
        string $user_name,
        string $user_email,
        string $user_password,
        string $user_type = '',
        string $user_image = '',
        string $user_dob = '',
        string $user_bio = '',
        string $user_phone = '',
        string $user_location = '',
        string $lga = '',
        string $user_address = '',
        ?int $user_rating = 0,
        ?string $user_gender = null,
        ?int $user_likes = 0,
        ?int $user_views = 0,
        ?int $user_shares = 0,
        ?int $user_fee = 0,
        string $user_preference = '',
        string $user_services = '',
        string $vkey = '',
        int $verified = 0,
        string $payment_status = 'pending',
        string $date_added = ''
    ): bool {
        if (empty($user_email) || empty($user_password)) {
            return false;
        }
    
        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
    
        // ✅ Sanitize values
        $user_name     = htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
        $user_email    = filter_var($user_email, FILTER_SANITIZE_EMAIL);
        $user_bio      = htmlspecialchars($user_bio, ENT_QUOTES, 'UTF-8');
        $user_address  = htmlspecialchars($user_address, ENT_QUOTES, 'UTF-8');
        $user_location = htmlspecialchars($user_location, ENT_QUOTES, 'UTF-8');
        $lga           = htmlspecialchars($lga, ENT_QUOTES, 'UTF-8');
        $user_phone    = htmlspecialchars($user_phone, ENT_QUOTES, 'UTF-8');
        $user_dob      = htmlspecialchars($user_dob, ENT_QUOTES, 'UTF-8');
        $user_gender   = $user_gender ? htmlspecialchars($user_gender, ENT_QUOTES, 'UTF-8') : null;
        $vkey          = $vkey ?: md5(time() . $user_email);
    
        // ✅ Check if email exists
        $stmt = $this->conn->prepare("SELECT id FROM user_profile WHERE user_email = ?");
        if (!$stmt) {
            throw new \RuntimeException("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return false;
        }
        $stmt->close();
    
        // ✅ Hash password
        $hashedPassword = password_hash($user_password, PASSWORD_BCRYPT);
    
        // ✅ Default timestamp
        $date_added = $date_added ?: (new \DateTimeImmutable())->format('Y-m-d H:i:s');
    
        // ✅ Insert new user
        $stmt = $this->conn->prepare("INSERT INTO user_profile (
            user_name, user_email, user_password, user_type, user_image, user_dob, user_bio,
            user_phone, user_location, lga, user_address, user_rating, user_gender, user_likes,
            user_views, user_shares, user_fee, user_preference, user_services, vkey, verified, payment_status, date_added
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        if (!$stmt) {
            throw new \RuntimeException("Prepare failed: " . $this->conn->error);
        }
    
        // ✅ Correct bind_param string (23 values)
        $stmt->bind_param(
            "sssssssssssisisiiisssis",
            $user_name,        // s
            $user_email,       // s
            $hashedPassword,   // s
            $user_type,        // s
            $user_image,       // s
            $user_dob,         // s
            $user_bio,         // s
            $user_phone,       // s
            $user_location,    // s
            $lga,              // s
            $user_address,     // s
            $user_rating,      // i
            $user_gender,      // s
            $user_likes,       // i
            $user_views,       // i
            $user_shares,      // i
            $user_fee,         // i
            $user_preference,  // s
            $user_services,    // s
            $vkey,             // s
            $verified,         // i
            $payment_status,   // s
            $date_added        // s
        );
    
        $success = $stmt->execute();
        $stmt->close();
    
        return $success;
    }
    

    /**
     * Login user
     */
    public function login(string $email, string $user_password): bool
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $stmt = $this->conn->prepare("SELECT id, user_password, user_type FROM user_profile WHERE user_email = ? AND verified = 1 LIMIT 1");
        if (!$stmt) {
            throw new \RuntimeException("Prepare failed: " . $this->conn->error);
        }
    
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
    
        // Check if user exists
        if ($stmt->num_rows === 0) {
            $stmt->close();
            return false; // No such verified user
        }
    
        $stmt->bind_result($id, $hashedPassword, $user_type);
        $stmt->fetch();
        $stmt->close();
    
        // Verify password
        if (!empty($hashedPassword) && password_verify($user_password, $hashedPassword)) {
            $_SESSION['user_id']   = $id;
            $_SESSION['user_type'] = $user_type;
            return true;
        }
    
        return false; // Wrong password
    }
    
    /**
     * Logout user
     */
    public function logout(): void
    {
        session_unset();
        session_destroy();
    }


    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
        
    }

    // redirect user if not logged in
    public function checkLogin(bool $redirect = false): bool
    {
        if (empty($_SESSION['user_id'])) {
            if ($redirect) {
                header("Location: ../public/login");
                exit;
            }
            return false;
        }
        return true;
    }


    public function getUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Forgot Password Module
     */
    public function forgotPassword(string $email, string $vkey = '', string $created_at = ''): bool
    {
        $checkEmail = $this->conn->prepare("SELECT email FROM user_profile WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $result = $checkEmail->get_result();

        if ($result->num_rows === 0) {
            return false;
        }

        $insertEmail = $this->conn->prepare("INSERT INTO forgot_password (email, vkey, created_at) VALUES (?, ?, ?)");
        $insertEmail->bind_param("sss", $email, $vkey, $created_at);

        return $insertEmail->execute();
    }

    /**
     * Send message module
     */
    public function sendMessage(
        string $sender_email,
        string $name,
        string $subject,
        string $compose,
        string $receiver_email,
        int $has_read = 0,
        int $is_receiver_deleted = 0,
        int $is_sender_deleted = 0,
        string $date = ''
    ): bool {
        $date = $date ?: date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("INSERT INTO messages (sender_email, name, subject, compose, receiver_email, has_read, is_receiver_deleted, is_sender_deleted, date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            'ssssssiss',
            $sender_email, $name, $subject, $compose, $receiver_email,
            $has_read, $is_receiver_deleted, $is_sender_deleted, $date
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Contact us module
     */
    public function contactUs(
        string $fullname,
        string $subject, string $email,
        string $message, string $date = ''
    ): bool {
        $date = date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("INSERT INTO contact (fullname, email, subject, message , date)
            VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $email, $subject, $message ,$date);

        return $stmt->execute();
    }

    /**
     * Newsletter subscription
     */
    public function subscribe(string $email, string $date = ''): bool
    {
        $date = $date ?: date('Y-m-d H:i:s');
        $stmt = $this->conn->prepare("INSERT INTO subscription (email, date) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $date);

        return $stmt->execute();
    }


    public function addComment(
        string $sender_email, string $sender_name,
        string $comment, int $user_id, string $date = ''
    ): bool {
        // ✅ Use current timestamp if no date passed
        $date = $date ?: date('Y-m-d H:i:s');
    
        // ✅ Prepare SQL
        $stmt = $this->conn->prepare("
            INSERT INTO sp_comment (sender_email, sender_name, comment, user_id, date) 
            VALUES (?, ?, ?, ?, ?)
        ");
    
        if (!$stmt) {
            // Debug error
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
    
        // ✅ Bind params: sender_email (s), sender_name (s), comment (s), user_id (i), date (s)
        $stmt->bind_param("sssis", $sender_email, $sender_name, $comment, $user_id, $date);
    
        // ✅ Execute
        $success = $stmt->execute();
    
        $stmt->close();
    
        return $success;
    }
    

    public function reportUser(
        string $vendor_name, string $vendor_email,
        string $user_type,  string $sender_email,
        string $issue, string $date = ''
    ): bool {
        $date = $date ?: date('Y-m-d H:i:s');
    
        // ✅ Prepare SQL — match with your actual table columns
        $stmt = $this->conn->prepare("
            INSERT INTO reportUser (vendor_name, vendor_email, user_type, sender_email, issue, date) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
    
        if (!$stmt) {
            return false; // prepare failed
        }
    
        // ✅ Bind parameters
        $stmt->bind_param("ssssss", 
            $vendor_name,  $vendor_email,  $user_type, 
            $sender_email,  $issue,  $date
        );
    
        return $stmt->execute();
    }
    

    /**
     * Get time ago
     */
    public function timeAgo(string $date): string
    {
        $datetime1 = new DateTime($date);
        $datetime2 = new DateTime();
        $interval = $datetime1->diff($datetime2);

        if ($interval->y > 0) return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        if ($interval->m > 0) return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        if ($interval->d > 0) return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        if ($interval->h > 0) return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        if ($interval->i > 0) return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        return 'Just now';
    }

    /**
     * Get years ago
     */
    public function yearsAgo(string $date): string
    {
        $from = new DateTime($date);
        $now = new DateTime();
        $years = $from->diff($now)->y;

        return $years === 0 ? "Less than a year old" : "$years year" . ($years > 1 ? "s" : "") . " old";
    }
}