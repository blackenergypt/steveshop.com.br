// Animação dos contadores de estatísticas
document.addEventListener('DOMContentLoaded', () => {
    // Função para verificar se um elemento está visível na tela
    function isElementInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    // Função para animar contadores
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-count');
        
        for (const counter of counters) {
            // Verifica se o contador está visível e se ainda não foi animado
            if (isElementInViewport(counter) && !counter.classList.contains('animated')) {
                counter.classList.add('animated');
                
                const target = Number.parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // duração da animação em ms
                const startTime = Date.now();
                const startValue = 0;
                
                function updateCounter() {
                    const currentTime = Date.now();
                    const elapsedTime = currentTime - startTime;
                    
                    if (elapsedTime < duration) {
                        const progress = elapsedTime / duration;
                        const currentValue = Math.floor(startValue + progress * (target - startValue));
                        counter.textContent = currentValue;
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                }
                
                updateCounter();
            }
        }
    }
    
    // Slider de depoimentos
    const testimonialSlider = document.querySelector('.testimonials-slider');
    if (testimonialSlider) {
        const track = testimonialSlider.querySelector('.testimonials-track');
        const cards = track.querySelectorAll('.testimonial-card');
        const prevBtn = testimonialSlider.querySelector('.prev-btn');
        const nextBtn = testimonialSlider.querySelector('.next-btn');
        
        let currentIndex = 0;
        let cardWidth = cards[0].offsetWidth + 30; // largura + gap
        const visibleCards = window.innerWidth < 768 ? 1 : window.innerWidth < 992 ? 2 : 3;
        
        // Atualiza a largura dos cartões ao redimensionar a janela
        window.addEventListener('resize', () => {
            cardWidth = cards[0].offsetWidth + 30;
        });
        
        // Clone os cartões para criar um efeito de loop
        const clonedCards = Array.from(cards).map(card => card.cloneNode(true));
        for (const card of clonedCards) {
            track.appendChild(card);
        }
        
        // Função para mover o slider
        function moveSlider(direction) {
            if (direction === 'next') {
                currentIndex++;
                if (currentIndex >= cards.length) {
                    // Reset suave após o último cartão
                    setTimeout(() => {
                        track.style.transition = 'none';
                        currentIndex = 0;
                        track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
                        setTimeout(() => {
                            track.style.transition = 'transform 0.5s ease';
                        }, 50);
                    }, 500);
                }
            } else {
                currentIndex--;
                if (currentIndex < 0) {
                    // Reset suave antes do primeiro cartão
                    setTimeout(() => {
                        track.style.transition = 'none';
                        currentIndex = cards.length - 1;
                        track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
                        setTimeout(() => {
                            track.style.transition = 'transform 0.5s ease';
                        }, 50);
                    }, 500);
                }
            }
            
            track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
        }
        
        // Adicionar event listeners aos botões
        if (prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                moveSlider('prev');
            });
            
            nextBtn.addEventListener('click', () => {
                moveSlider('next');
            });
        }
        
        // Auto slide a cada 5 segundos
        let autoSlide = setInterval(() => {
            moveSlider('next');
        }, 5000);
        
        // Parar auto slide ao passar o mouse
        testimonialSlider.addEventListener('mouseenter', () => {
            clearInterval(autoSlide);
        });
        
        // Retomar auto slide ao tirar o mouse
        testimonialSlider.addEventListener('mouseleave', () => {
            autoSlide = setInterval(() => {
                moveSlider('next');
            }, 5000);
        });
    }
    
    // Inicializar animação de contadores ao carregar a página
    animateCounters();
    
    // Verificar contadores ao rolar a página
    window.addEventListener('scroll', animateCounters);
}); 