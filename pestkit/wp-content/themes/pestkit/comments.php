<?php
if (post_password_required()) return;
?>

<div class="comment-form-wrapper bg-light rounded p-5 mt-5">
    <div class="container">
        <h3 class="mb-4 text-dark fw-bold text-center">Leave A Feedback</h3>

        <!-- Добавили класс novalidate, чтобы отключить уродливые дефолтные подсказки браузера -->
        <form id="custom-ajax-feedback-form" novalidate>
            <div style="display:none !important; visibility:hidden !important;">
                <input type="text" name="honeypot_field" value="" autocomplete="off">
            </div>

            <input type="hidden" name="action" value="submit_custom_feedback_ajax">
            <input type="hidden" name="post_id" value="<?php echo get_the_ID(); ?>">
            <?php wp_nonce_field('feedback_nonce_action', 'feedback_nonce'); ?>

            <div class="row g-3">
                <div class="col-md-6 position-relative">
                    <input type="text" name="author" class="form-control py-3" placeholder="Your Name *">
                    <div class="error-text-under" id="error-author"></div>
                </div>
                <div class="col-md-6 position-relative">
                    <input type="email" name="email" class="form-control py-3" placeholder="Your Email *">
                    <div class="error-text-under" id="error-email"></div>
                </div>

                <!-- Rating field -->
                <div class="col-12 mb-2">
                    <div class="d-flex align-items-center">
                        <span class="text-dark fw-bold me-3">Your Rating:</span>
                        <div class="star-rating-wrapper">
                            <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    <div class="error-text-under" id="error-rating"></div>
                </div>

                <div class="col-12 position-relative">
                    <textarea name="comment" class="form-control py-3" rows="5" placeholder="Leave your feedback here"></textarea>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="comment-submit-btn btn btn-primary border-0 rounded-pill py-3 px-5 text-uppercase w-100">
                        Submit Feedback
                    </button>
                </div>
            </div>
        </form>

        <!-- УНИВЕРСАЛЬНЫЙ ГЛОБАЛЬНЫЙ ПОПАП (Изначально скрыт) -->
        <div id="feedback-global-popup" style="display: none;">
            <div class="text-center p-5 rounded shadow bg-white border border-2 border-primary" style="max-width: 450px; width: 90%; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;">
                <!-- Сюда JS будет вставлять иконку (зеленую галочку или красный крестик) -->
                <i id="popup-icon" class="fa fa-check-circle display-4 text-primary mb-3"></i>

                <!-- Сюда JS вставит динамический заголовок -->
                <h4 id="popup-title" class="fw-bold text-dark mb-2">Thank you!</h4>

                <!-- Сюда JS вставит текст ошибки или успеха из PHP -->
                <p id="popup-message" class="text-muted mb-4">Message text...</p>

                <button id="close-feedback-popup" class="btn btn-primary border-0 rounded-pill px-5 py-2 text-uppercase fw-bold text-dark" style="background-color: #FFEB3B !important;">Close</button>
            </div>
        </div>
    </div>
</div>