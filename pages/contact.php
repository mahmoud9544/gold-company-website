<?php
$pageTitle = 'اتصل بنا';
$messageSent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name)) {
        $errors[] = 'الاسم مطلوب';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صحيح';
    }
    if (empty($message)) {
        $errors[] = 'الرسالة مطلوبة';
    }

    if (empty($errors)) {
        $messageSent = true;
    }
}
?>
<?php include '../includes/header.php'; ?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>اتصل بنا</h1>
            <p>نحن هنا لمساعدتك — تواصل معنا في أي وقت</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper">
                    <h2>أرسل لنا رسالة</h2>

                    <?php if ($messageSent): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            تم إرسال رسالتك بنجاح! سنتواصل معك في أقرب وقت.
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name">الاسم الكامل *</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                   placeholder="أدخل اسمك الكامل">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">البريد الإلكتروني *</label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                       placeholder="example@email.com">
                            </div>
                            <div class="form-group">
                                <label for="phone">رقم الهاتف</label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                                       placeholder="01xxxxxxxxx">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">الموضوع</label>
                            <select id="subject" name="subject">
                                <option value="">اختر الموضوع</option>
                                <option value="inquiry" <?php echo ($subject ?? '') === 'inquiry' ? 'selected' : ''; ?>>استفسار عام</option>
                                <option value="order" <?php echo ($subject ?? '') === 'order' ? 'selected' : ''; ?>>طلب شراء</option>
                                <option value="custom" <?php echo ($subject ?? '') === 'custom' ? 'selected' : ''; ?>>تصميم خاص</option>
                                <option value="complaint" <?php echo ($subject ?? '') === 'complaint' ? 'selected' : ''; ?>>شكوى</option>
                                <option value="other" <?php echo ($subject ?? '') === 'other' ? 'selected' : ''; ?>>أخرى</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="message">الرسالة *</label>
                            <textarea id="message" name="message" rows="5" required
                                      placeholder="اكتب رسالتك هنا..."><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> إرسال الرسالة
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info-wrapper">
                    <h2>معلومات التواصل</h2>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>العنوان</h3>
                            <p>طلب اونلاين</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-content">
                            <h3>الهاتف</h3>
                            <p dir="ltr">01124138988</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h3>البريد الإلكتروني</h3>
                            <p>mahmoudramadan9544@gmail.com</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h3>ساعات العمل</h3>
                            <p>السبت - الخميس: 10:00 ص - 10:00 م</p>
                            <p>الجمعة: 2:00 م - 10:00 م</p>
                        </div>
                    </div>

                    <!-- Map Placeholder -->
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt"></i>
                        <p>موقعنا على الخريطة</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
