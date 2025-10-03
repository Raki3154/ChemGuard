// Subtle button press animation and header shadow on scroll
(function(){
    const add = (el, ev, fn) => el && el.addEventListener(ev, fn, { passive: true });

    // Button micro-interaction
    document.querySelectorAll('a.btn, button.btn, button').forEach((el) => {
        add(el, 'mousedown', () => { el.style.transform = 'translateY(1px) scale(0.99)'; });
        add(el, 'mouseup', () => { el.style.transform = 'translateY(0) scale(1)'; });
        add(el, 'mouseleave', () => { el.style.transform = 'translateY(0) scale(1)'; });
        add(el, 'touchstart', () => { el.style.transform = 'translateY(1px) scale(0.99)'; });
        add(el, 'touchend', () => { el.style.transform = 'translateY(0) scale(1)'; });
    });

    // Header shadow when scrolling
    const header = document.querySelector('header');
    let lastY = 0;
    add(window, 'scroll', () => {
        const y = window.scrollY || window.pageYOffset;
        if(!header) return;
        if(y > 4 && lastY <= 4){
            header.style.boxShadow = '0 6px 18px rgba(0,0,0,0.25)';
        } else if(y <= 4 && lastY > 4){
            header.style.boxShadow = 'none';
        }
        lastY = y;
    });

    // Page fade-in removed

    // Theme toggle removed per request
})();
// Show boiler popup automatically on login
window.onload = function(){
    if(sessionStorage.getItem("popupShown") !== "true"){
        document.getElementById("boilerModal").style.display = "flex"; // use flex for centering
        document.querySelector("model-viewer").style.pointerEvents = "none";
        sessionStorage.setItem("popupShown","true");
    }
};

document.querySelector(".close").onclick = function() {
    document.getElementById("boilerModal").style.display = "none";
    document.querySelector("model-viewer").style.pointerEvents = "auto";
};
// Chatbot trigger functionality
        document.getElementById('chatbotTrigger').addEventListener('click', function() {
            // In a real implementation, this would open the chatbot interface
            alert('AI Sustainability Assistant: Hello! I can help you optimize your industrial processes for better efficiency and lower emissions. This feature will be fully implemented in the next version!');
            
            // For demo purposes - redirect to a placeholder chatbot page
            // window.location.href = 'chatbot.php';
        });
        
        // Add subtle animation to cards on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card, .feature-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            cards.forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
// Popup modal auto-show once per session
window.onload = function() {
    if(sessionStorage.getItem("popupShown") !== "true") {
        document.getElementById("boilerModal").style.display = "block";
        sessionStorage.setItem("popupShown", "true");
    }
};

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById("boilerModal");
    if(event.target === modal) {
        modal.style.display = "none";
    }
};

// Animate cards on scroll
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card, .feature-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    cards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
});

// Chatbot trigger click
document.getElementById('chatbotTrigger').addEventListener('click', function() {
    window.location.href = 'chatbot.php';
});
