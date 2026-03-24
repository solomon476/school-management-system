        </main>
    </div>
</div>

<script>
    // Handle Mobile Sidebar Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const mobileOverlay = document.getElementById('mobile-overlay');
    const closeMobileMenuBtn = document.getElementById('close-mobile-menu');

    function toggleMenu() {
        const isClosed = mobileSidebar.classList.contains('-translate-x-full');
        if (isClosed) {
            mobileSidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.remove('hidden');
        } else {
            mobileSidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        }
    }

    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleMenu);
    if (closeMobileMenuBtn) closeMobileMenuBtn.addEventListener('click', toggleMenu);
    if (mobileOverlay) mobileOverlay.addEventListener('click', toggleMenu);
</script>

</body>
</html>
