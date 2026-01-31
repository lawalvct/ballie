
@include('layouts.website.header')
        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

       @include('layouts.website.footer')

       <!-- WhatsApp Floating Button (only shown if brand has WhatsApp) -->
       @if($brand['whatsapp'] ?? null)
       <a href="https://wa.me/{{ $brand['whatsapp'] }}?text=Hi,%20I'm%20interested%20in%20{{ urlencode($brand['name'] ?? 'Ballie') }}%20Business%20Management%20Software"
          target="_blank"
          id="whatsapp-float"
          class="whatsapp-float"
          aria-label="Chat on WhatsApp">
           <svg viewBox="0 0 32 32" width="32" height="32" fill="white">
               <path d="M16 0C7.164 0 0 7.164 0 16c0 2.825.738 5.488 2.031 7.794L0 32l8.394-2.031C10.7 31.262 13.363 32 16 32c8.836 0 16-7.164 16-16S24.836 0 16 0zm0 29.333c-2.544 0-4.944-.706-6.981-1.931l-.5-.3-5.181 1.25 1.25-5.181-.3-.5C2.706 20.944 2 18.544 2 16 2 8.269 8.269 2 16 2s14 6.269 14 14-6.269 13.333-14 13.333zm7.738-10.131c-.425-.213-2.513-1.238-2.9-1.381-.388-.144-.669-.213-.95.213-.281.425-1.088 1.381-1.331 1.663-.244.281-.494.319-.919.106-.425-.213-1.794-.663-3.419-2.113-1.263-1.125-2.119-2.519-2.363-2.944-.244-.425-.025-.656.188-.869.194-.194.425-.5.638-.75.213-.25.281-.425.425-.706.144-.281.075-.531-.038-.75-.113-.213-.95-2.288-1.3-3.131-.344-.825-.694-.713-.95-.725-.244-.013-.525-.013-.806-.013s-.738.106-1.125.531c-.388.425-1.475 1.444-1.475 3.519s1.513 4.081 1.725 4.363c.213.281 2.981 4.55 7.219 6.381 1.006.431 1.794.688 2.406.881.1.031 1.913.581 2.181.581.269 0 2.063-.844 2.356-1.656.294-.813.294-1.506.206-1.656-.088-.15-.369-.244-.794-.456z"/>
           </svg>
       </a>
       @endif

       <style>
           .whatsapp-float {
               position: fixed;
               width: 60px;
               height: 60px;
               bottom: 40px;
               right: 40px;
               background-color: #25d366;
               color: #FFF;
               border-radius: 50px;
               text-align: center;
               font-size: 30px;
               box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
               z-index: 1000;
               display: flex;
               align-items: center;
               justify-content: center;
               transition: all 0.3s ease;
               animation: pulse 2s infinite;
               cursor: move;
           }

           .whatsapp-float.dragging {
               animation: none;
               transition: none;
           }

           .whatsapp-float:hover {
               background-color: #128c7e;
               transform: scale(1.1);
               box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.4);
           }

           @keyframes pulse {
               0% {
                   box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
               }
               70% {
                   box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
               }
               100% {
                   box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
               }
           }

           @media screen and (max-width: 768px) {
               .whatsapp-float {
                   width: 50px;
                   height: 50px;
                   bottom: 20px;
                   right: 20px;
                   font-size: 25px;
               }
           }
       </style>

       <script>
           (function() {
               const btn = document.getElementById('whatsapp-float');
               let isDragging = false;
               let startX, startY, startBottom, startRight;

               btn.addEventListener('mousedown', function(e) {
                   if (e.button !== 0) return;
                   isDragging = true;
                   btn.classList.add('dragging');
                   startX = e.clientX;
                   startY = e.clientY;
                   startBottom = parseInt(window.getComputedStyle(btn).bottom);
                   startRight = parseInt(window.getComputedStyle(btn).right);
                   e.preventDefault();
               });

               document.addEventListener('mousemove', function(e) {
                   if (!isDragging) return;
                   const deltaX = startX - e.clientX;
                   const deltaY = startY - e.clientY;
                   btn.style.right = (startRight + deltaX) + 'px';
                   btn.style.bottom = (startBottom + deltaY) + 'px';
               });

               document.addEventListener('mouseup', function(e) {
                   if (isDragging) {
                       isDragging = false;
                       btn.classList.remove('dragging');
                       if (Math.abs(e.clientX - startX) < 5 && Math.abs(e.clientY - startY) < 5) {
                           return;
                       }
                       e.preventDefault();
                   }
               });

               btn.addEventListener('click', function(e) {
                   if (Math.abs(e.clientX - startX) >= 5 || Math.abs(e.clientY - startY) >= 5) {
                       e.preventDefault();
                   }
               });
           })();
       </script>

