import { isValidEmail, isValidPhone, serializeForm } from './validation.js';

(function(){
    document.addEventListener('DOMContentLoaded', () => {
        // Mobile menu toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const mainNav = document.getElementById('mainNav');
        if (mobileBtn && mainNav) {
            mobileBtn.addEventListener('click', () => mainNav.classList.toggle('open'));
        }

        // Smooth anchor scrolling
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', (e) => {
                const href = a.getAttribute('href');
                if (href === '#') return;
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({behavior:'smooth', block:'start'});
                }
            });
        });

        // Stats counter
        const stats = document.querySelectorAll('.stat-number');
        if (stats.length) {
            const onScroll = () => {
                stats.forEach(s => {
                    if (s.dataset.done) return;
                    const rect = s.getBoundingClientRect();
                    if (rect.top < window.innerHeight - 50) {
                        const end = parseInt(s.textContent.replace(/\D/g,'')) || 0;
                        animateValue(s, 0, end, 1500);
                        s.dataset.done = '1';
                    }
                });
            };
            window.addEventListener('scroll', onScroll);
            onScroll();
        }

        function animateValue(el, start, end, duration) {
            const range = end - start; 
            let startTime = null;
            function step(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                el.textContent = Math.floor(progress * range + start);
                if (progress < 1) window.requestAnimationFrame(step);
            }
            window.requestAnimationFrame(step);
        }

        // Testimonials rotate
        const testimonials = document.querySelectorAll('.testimonials .testimonial-card');
        if (testimonials.length>1) {
            let idx = 0;
            testimonials.forEach((t,i) => t.style.display = i===0 ? 'block' : 'none');
            setInterval(() => {
                testimonials[idx].style.display = 'none';
                idx = (idx+1) % testimonials.length;
                testimonials[idx].style.display = 'block';
            }, 4000);
        }

        // Form handling
        attachFormHandler('contactForm', 'backend/submit-contact.php');
        attachFormHandler('footerContactForm', 'backend/submit-contact.php');
        attachFormHandler('admissionForm', 'backend/submit-admission.php');

        function attachFormHandler(formId, endpoint) {
            const form = document.getElementById(formId);
            if (!form) return;
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('input[type="submit"]');
                const data = serializeForm(form);

                // Basic client-side validation
                if (data.email && !isValidEmail(data.email)) return showAlert(form, 'Please enter a valid email address', 'error');
                if (data.phone && !isValidPhone(data.phone)) return showAlert(form, 'Please enter a valid phone number', 'error');
                // Require a name and message for contact forms
                if (formId === 'contactForm' && (!data.name || !data.message)) return showAlert(form, 'Please complete required fields', 'error');
                if (formId === 'admissionForm' && (!data.contactName || !data.contactPhone || !data.contactEmail)) return showAlert(form, 'Please complete required fields for admission', 'error');

                // UI Lock
                if (submitBtn) { submitBtn.disabled = true; submitBtn.dataset.orig = submitBtn.textContent; submitBtn.textContent = 'Sending...'; }

                try {
                    const res = await fetch(endpoint, {method:'POST', headers:{'Accept':'application/json'}, body: new URLSearchParams(data)});
                    const json = await res.json();
                    if (json.success) {
                        form.reset();
                        showAlert(form, json.message || 'Thank you! We received your message.', 'success');
                    } else {
                        showAlert(form, json.message || 'There was a problem, please try again later.', 'error');
                    }
                } catch (err) {
                    showAlert(form, 'Network error — please try again later.', 'error');
                } finally {
                    if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = submitBtn.dataset.orig || 'Submit'; }
                }
            });
        }

        function showAlert(form, msg, type) {
            const existing = form.querySelector('.site-form-alert');
            if (existing) existing.remove();
            const alert = document.createElement('div');
            alert.className = 'site-form-alert ' + (type === 'success' ? 'alert-success' : 'alert-error');
            alert.style.margin = '10px 0';
            alert.textContent = msg;
            form.prepend(alert);
            setTimeout(()=> alert.classList.add('fade-in'), 20);
            setTimeout(()=> { try{ alert.remove(); } catch(e){} }, 6000);
        }

    });
})();