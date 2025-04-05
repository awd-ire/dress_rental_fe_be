 document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
                document.getElementById(this.dataset.section).style.display = 'block';
            });
        });