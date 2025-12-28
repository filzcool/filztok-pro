<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>FilzTok Pro - Download TikTok Content</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #0a0a0f; 
            font-family: 'Inter', sans-serif; 
            overflow-x: hidden; 
            -webkit-tap-highlight-color: transparent;
        }
        .gradient-bg { 
            position: fixed; 
            inset: 0; 
            background: radial-gradient(1200px 600px at 10% -10%, #6d28d9 0%, transparent 50%), 
                        radial-gradient(1000px 500px at 110% 10%, #ec4899 0%, transparent 50%), 
                        #0a0a0f; 
            z-index: -1; 
        }
        .glass-card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4); 
        }
        .glass-button { 
            background: rgba(255, 255, 255, 0.08); 
            border: 1px solid rgba(255, 255, 255, 0.15); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .glass-button:hover { 
            background: rgba(255, 255, 255, 0.12); 
            transform: translateY(-2px); 
            box-shadow: 0 10px 24px rgba(99, 102, 241, 0.3); 
        }
        .primary-button { 
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); 
        }
        .primary-button:hover { 
            filter: brightness(1.1); 
            transform: scale(1.02); 
        }
        .slide-anim { 
            animation: slideIn 0.4s cubic-bezier(0.23, 1, 0.32, 1); 
        }
        @keyframes slideIn { 
            from { opacity: 0; transform: translateY(20px) scale(0.95); } 
            to { opacity: 1; transform: translateY(0) scale(1); } 
        }
        .toast { 
            backdrop-filter: blur(10px); 
            -webkit-backdrop-filter: blur(10px); 
        }
        .profile-pic-overlay { 
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.4), rgba(236, 72, 153, 0.4)); 
        }
        .progress-fill { 
            transition: width 0.3s ease; 
        }
        .skeleton { 
            background: linear-gradient(90deg, rgba(255,255,255,0.05) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.05) 75%); 
            background-size: 200% 100%; 
            animation: loading 1.5s infinite; 
        }
        @keyframes loading { 
            0% { background-position: 200% 0; } 
            100% { background-position: -200% 0; } 
        }
        .quality-badge { 
            background: rgba(99, 102, 241, 0.2); 
            color: #818cf8; 
            font-size: 0.6rem; 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-weight: 700; 
        }
        .stat-item:hover { 
            background: rgba(255, 255, 255, 0.05); 
            border-radius: 8px; 
        }
    </style>
</head>
<body class="text-white min-h-screen p-4 flex flex-col items-center">
    <div class="gradient-bg"></div>

    <!-- Header -->
    <nav class="w-full max-w-xl glass-card rounded-2xl p-4 flex justify-between items-center mb-6 slide-anim">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center shadow-lg">
                <i class="fab fa-tiktok"></i>
            </div>
            <span class="font-extrabold font-display text-2xl">
                Filz<span class="text-indigo-400">Tok</span><span class="text-xs text-gray-400 ml-1">Pro</span>
            </span>
        </div>
        <div class="flex gap-2">
            <button onclick="showHelp()" class="glass-button w-10 h-10 rounded-full flex items-center justify-center hover:scale-105 transition">
                <i class="fas fa-question"></i>
            </button>
            <a href="https://saweria.co/FilzDeveloper" target="_blank" class="glass-button w-10 h-10 rounded-full flex items-center justify-center text-yellow-400 hover:scale-105 transition">
                <i class="fas fa-coffee"></i>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="w-full max-w-xl slide-anim" style="animation-delay: 0.1s;">
        <h1 class="text-4xl font-extrabold text-center mb-6 bg-gradient-to-r from-white to-indigo-400 bg-clip-text text-transparent leading-tight">
            Get TikTok Content<br><span class="text-indigo-400">Without Watermark</span>
        </h1>

        <!-- Input Section -->
        <div class="glass-card rounded-2xl p-4 mb-6">
            <div class="flex gap-2 mb-3">
                <input type="text" id="urlInput" placeholder="Paste TikTok link here..." 
                       class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-indigo-400 focus:bg-white/10 transition"
                       autocomplete="off">
                <button onclick="pasteLink()" class="glass-button w-12 rounded-xl flex items-center justify-center hover:scale-105 transition" title="Paste from clipboard">
                    <i class="fas fa-paste"></i>
                </button>
                <button onclick="clearInput()" class="glass-button w-12 rounded-xl flex items-center justify-center hover:scale-105 transition" title="Clear">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button id="getButton" onclick="getContent()" class="w-full primary-button text-black font-bold py-3 rounded-xl flex items-center justify-center gap-2 hover:shadow-xl transition">
                <span id="btnText">Get Video</span>
                <span id="btnLoader" class="hidden animate-spin"><i class="fas fa-circle-notch"></i></span>
            </button>
            <div id="progressBar" class="w-full h-1 bg-white/10 rounded-full overflow-hidden mt-3 hidden">
                <div id="progressFill" class="progress-fill h-full bg-gradient-to-r from-indigo-500 to-pink-500" style="width: 0%"></div>
            </div>
        </div>

        <!-- Result Card -->
        <div id="resultCard" class="glass-card rounded-2xl overflow-hidden hidden">
            <!-- Profile Header -->
            <div class="p-4 border-b border-white/10 flex items-center gap-4">
                <div class="relative w-16 h-16 rounded-full overflow-hidden group cursor-pointer" onclick="downloadProfilePic()">
                    <img id="profilePic" src="" class="w-full h-full object-cover" alt="Profile">
                    <div class="absolute inset-0 profile-pic-overlay opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                        <span class="text-white text-xs font-bold"><i class="fas fa-download"></i></span>
                    </div>
                </div>
                <div class="flex-1">
                    <p id="username" class="font-bold text-lg">@username</p>
                    <p class="text-xs text-gray-400">Creator</p>
                </div>
                <button onclick="copyUsername()" class="glass-button w-10 h-10 rounded-full flex items-center justify-center hover:scale-105 transition" title="Copy username">
                    <i class="fas fa-copy"></i>
                </button>
                <a id="profileLink" target="_blank" class="glass-button w-10 h-10 rounded-full flex items-center justify-center hover:scale-105 transition" title="Open profile">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>

            <!-- Content -->
            <div class="p-4 space-y-4">
                <!-- Caption -->
                <div>
                    <p id="caption" class="text-sm text-gray-200 mb-2 leading-relaxed"></p>
                    <div id="hashtags" class="flex flex-wrap gap-1"></div>
                </div>

                <!-- Statistics -->
                <div class="grid grid-cols-3 gap-2 bg-white/5 rounded-xl p-3">
                    <div class="text-center stat-item p-2 transition">
                        <div id="plays" class="font-bold text-lg text-white">0</div>
                        <div class="text-xs text-gray-400">Plays</div>
                    </div>
                    <div class="text-center stat-item p-2 transition">
                        <div id="likes" class="font-bold text-lg text-white">0</div>
                        <div class="text-xs text-gray-400">Likes</div>
                    </div>
                    <div class="text-center stat-item p-2 transition">
                        <div id="shares" class="font-bold text-lg text-white">0</div>
                        <div class="text-xs text-gray-400">Shares</div>
                    </div>
                </div>

                <!-- Media Content -->
                <div id="mediaSection"></div>

                <!-- Music Section -->
                <div id="musicSection" class="glass-card p-3 rounded-xl hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-500 to-indigo-500 flex items-center justify-center shadow-lg">
                            <i class="fas fa-music"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold">Original Audio</p>
                            <p id="musicTitle" class="text-xs text-gray-400 truncate"></p>
                        </div>
                        <button onclick="previewAudio()" class="glass-button px-3 py-2 rounded-lg text-xs hover:scale-105 transition">
                            <i class="fas fa-play"></i> Preview
                        </button>
                        <button onclick="downloadMusic()" class="primary-button px-4 py-2 rounded-lg text-xs text-black font-bold hover:scale-105 transition">
                            <i class="fas fa-download"></i> MP3
                        </button>
                    </div>
                </div>

                <!-- Batch Download -->
                <div id="batchSection" class="border-t border-white/10 pt-4 hidden">
                    <button onclick="downloadAll()" class="w-full glass-button py-3 rounded-xl font-bold text-sm hover:shadow-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-archive"></i> Download All Media (ZIP)
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Audio Preview Modal -->
    <div id="audioModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl p-4 w-full max-w-sm">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-white">Audio Preview</h3>
                <button onclick="closeAudioModal()" class="text-gray-400 hover:text-white transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <audio id="audioPlayer" controls class="w-full"></audio>
        </div>
    </div>

    <!-- Help Modal -->
    <div id="helpModal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg text-white">FilzTok Pro Guide</h3>
                <button onclick="closeHelpModal()" class="text-gray-400 hover:text-white text-xl transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4 text-sm text-gray-300">
                <div>
                    <h4 class="font-bold text-white mb-2"><i class="fas fa-link text-indigo-400"></i> Quick Steps:</h4>
                    <ol class="list-decimal ml-5 space-y-1">
                        <li>Copy TikTok video/photo link</li>
                        <li>Paste in the input field (auto-detect)</li>
                        <li>Click "Get Video" button</li>
                        <li>Choose quality and download</li>
                    </ol>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-2"><i class="fas fa-star text-yellow-400"></i> All Features:</h4>
                    <ul class="list-disc ml-5 space-y-1">
                        <li>✅ Video download (HD/SD/With Watermark)</li>
                        <li>✅ Photo slideshow (individual + ZIP batch)</li>
                        <li>✅ Profile picture download</li>
                        <li>✅ Music/audio extraction</li>
                        <li>✅ Statistics (plays, likes, shares)</li>
                        <li>✅ Hashtag extraction</li>
                        <li>✅ Batch download all media</li>
                        <li>✅ Server-side download (bypass CORS)</li>
                    </ul>
                </div>
                <div class="bg-white/5 p-3 rounded-lg border border-white/10">
                    <p class="text-xs text-gray-400"><i class="fas fa-info-circle text-blue-400"></i> If download fails, click "Via Server" button or refresh page.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-6 left-1/2 -translate-x-1/2 toast bg-gray-800/90 border border-white/10 px-5 py-3 rounded-full text-sm font-bold flex items-center gap-3 opacity-0 transition-opacity duration-300 z-50 max-w-[90%]">
        <i id="toastIcon"></i> 
        <span id="toastText"></span>
    </div>

    <script>
        let currentData = null, slideIndex = 0, csrfToken = btoa(Math.random().toString(36) + Date.now());

        // Auto detect clipboard on load
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const text = await navigator.clipboard.readText();
                if (text?.includes('tiktok.com')) {
                    document.getElementById('urlInput').value = text;
                    showToast('Link detected from clipboard!', 'success');
                }
            } catch {}
        });

        // Toast notification system
        function showToast(message, type = 'info', duration = 3000) {
            const toast = document.getElementById('toast');
            const iconMap = {
                success: 'fas fa-check-circle text-green-400',
                error: 'fas fa-times-circle text-red-400',
                warning: 'fas fa-exclamation-triangle text-yellow-400',
                info: 'fas fa-info-circle text-blue-400'
            };
            
            document.getElementById('toastIcon').className = iconMap[type];
            document.getElementById('toastText').textContent = message;
            
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
            
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, duration);
        }

        // Utility functions
        async function pasteLink() {
            try {
                const text = await navigator.clipboard.readText();
                document.getElementById('urlInput').value = text;
                showToast('Link pasted!', 'success');
            } catch {
                showToast('Please paste manually', 'warning');
            }
        }

        function clearInput() {
            document.getElementById('urlInput').value = '';
            document.getElementById('resultCard').classList.add('hidden');
        }

        function updateProgress(percent) {
            document.getElementById('progressFill').style.width = percent + '%';
        }

        function formatNumber(num) {
            if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
            if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
            return num.toString();
        }

        // Main function to fetch content
        async function getContent() {
            const url = document.getElementById('urlInput').value.trim();
            if (!url) return showToast('Please paste a TikTok link!', 'error');
            
            const btn = document.getElementById('getButton');
            const loader = document.getElementById('btnLoader');
            const text = document.getElementById('btnText');
            
            btn.disabled = true;
            loader.classList.remove('hidden');
            text.classList.add('hidden');
            document.getElementById('resultCard').classList.add('hidden');
            document.getElementById('progressBar').classList.remove('hidden');
            updateProgress(10);

            try {
                updateProgress(30);
                
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({ url, csrfToken })
                });
                
                updateProgress(70);
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    updateProgress(100);
                    currentData = result.data;
                    renderContent();
                    document.getElementById('resultCard').classList.remove('hidden');
                    document.getElementById('resultCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    showToast('Content loaded successfully!', 'success');
                } else {
                    throw new Error(result.message || 'Failed to fetch content');
                }
            } catch (err) {
                showToast(err.message || 'An error occurred. Please try again.', 'error');
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
                text.classList.remove('hidden');
                setTimeout(() => document.getElementById('progressBar').classList.add('hidden'), 500);
            }
        }

        // Render content in UI
        function renderContent() {
            const data = currentData;
            
            // Profile info
            document.getElementById('profilePic').src = data.avatar;
            document.getElementById('username').textContent = '@' + data.username;
            document.getElementById('caption').textContent = data.title || '-';
            document.getElementById('profileLink').href = `https://www.tiktok.com/@${data.username}`;
            
            // Stats
            document.getElementById('plays').textContent = formatNumber(data.play_count);
            document.getElementById('likes').textContent = formatNumber(data.digg_count);
            document.getElementById('shares').textContent = formatNumber(data.share_count);
            
            // Hashtags
            const tagsContainer = document.getElementById('hashtags');
            tagsContainer.innerHTML = '';
            (data.hashtags || []).slice(0, 6).forEach(tag => {
                const span = document.createElement('span');
                span.className = 'px-2 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-[10px] border border-indigo-500/30';
                span.textContent = '#' + tag;
                tagsContainer.appendChild(span);
            });

            // Media content
            const mediaSection = document.getElementById('mediaSection');
            mediaSection.innerHTML = '';
            
            if (data.images && data.images.length > 0) {
                // Slideshow photos
                mediaSection.innerHTML = `
                    <div class="space-y-4">
                        <div class="relative aspect-square bg-black rounded-xl overflow-hidden shadow-2xl group">
                            <img id="slideImage" src="${data.images[0]}" class="w-full h-full object-contain">
                            <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 flex justify-between px-2 opacity-0 group-hover:opacity-100 transition">
                                <button onclick="changeSlide(-1)" class="w-10 h-10 bg-black/70 rounded-full flex items-center justify-center hover:bg-black/90 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button onclick="changeSlide(1)" class="w-10 h-10 bg-black/70 rounded-full flex items-center justify-center hover:bg-black/90 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/70 px-3 py-1 rounded-full text-xs font-medium">
                                ${data.images.length} Photos
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="downloadCurrentSlide()" class="glass-button py-3 rounded-xl font-bold text-sm hover:scale-105 transition flex items-center justify-center gap-2">
                                <i class="fas fa-image"></i> Download This
                            </button>
                            <button onclick="downloadAllSlides()" class="glass-button py-3 rounded-xl font-bold text-sm hover:scale-105 transition flex items-center justify-center gap-2">
                                <i class="fas fa-download"></i> All Photos (ZIP)
                            </button>
                        </div>
                        ${data.video ? `<button onclick="downloadMedia('${data.video}', 'slideshow_video.mp4')" class="w-full glass-button py-3 rounded-xl font-bold text-sm hover:scale-105 transition flex items-center justify-center gap-2">
                            <i class="fas fa-video"></i> Slideshow Video
                        </button>` : ''}
                    </div>
                `;
                window.slideImages = data.images;
                window.slideIndex = 0;
            } else if (data.video) {
                // Video player
                mediaSection.innerHTML = `
                    <div class="space-y-4">
                        <div class="aspect-[9/16] bg-black rounded-xl overflow-hidden shadow-2xl">
                            <video controls class="w-full h-full object-contain" src="${data.video}" poster="${data.cover || data.avatar}">
                                <source src="${data.video}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="downloadMedia('${data.video}', 'video_no_watermark.mp4')" class="primary-button py-3 rounded-xl font-bold text-sm text-black hover:scale-105 transition flex items-center justify-center gap-2">
                                <i class="fas fa-video"></i> HD Video
                            </button>
                            <button onclick="downloadMedia('${data.video}', 'video_no_watermark.mp4', true)" class="glass-button py-3 rounded-xl font-bold text-sm hover:scale-105 transition flex items-center justify-center gap-2">
                                <i class="fas fa-server"></i> Via Server
                            </button>
                        </div>
                    </div>
                `;
            }

            // Music section
            const musicSection = document.getElementById('musicSection');
            if (data.music) {
                musicSection.classList.remove('hidden');
                document.getElementById('musicTitle').textContent = data.music_title || 'Original Audio';
                document.getElementById('batchSection').classList.remove('hidden');
            } else {
                musicSection.classList.add('hidden');
            }
        }

        // Slideshow navigation
        function changeSlide(dir) {
            const images = window.slideImages;
            window.slideIndex = (window.slideIndex + dir + images.length) % images.length;
            document.getElementById('slideImage').src = images[window.slideIndex];
        }

        // Download functions
        function downloadCurrentSlide() {
            downloadMedia(window.slideImages[window.slideIndex], `slide_${window.slideIndex + 1}.jpg`);
        }

        async function downloadAllSlides() {
            showToast('Creating ZIP file...', 'info');
            try {
                const response = await fetch('batch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ type: 'slides', urls: window.slideImages, csrfToken })
                });
                
                if (!response.ok) throw new Error('ZIP creation failed');
                
                const blob = await response.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `tiktok_photos_${Date.now()}.zip`;
                a.click();
                URL.revokeObjectURL(url);
                showToast('ZIP created successfully!', 'success');
            } catch {
                showToast('ZIP failed, downloading individually...', 'warning');
                window.slideImages.forEach((img, i) => setTimeout(() => downloadMedia(img, `slide_${i + 1}.jpg`), i * 300));
            }
        }

        function downloadProfilePic() {
            downloadMedia(currentData.avatar, `${currentData.username}_profile.jpg`);
        }

        function downloadMusic() {
            downloadMedia(currentData.music, `${currentData.username}_audio.mp3`);
        }

        function previewAudio() {
            const modal = document.getElementById('audioModal');
            const player = document.getElementById('audioPlayer');
            player.src = currentData.music;
            modal.classList.remove('hidden');
            player.play().catch(() => showToast('Audio preview failed', 'error'));
        }

        function closeAudioModal() {
            const modal = document.getElementById('audioModal');
            const player = document.getElementById('audioPlayer');
            player.pause();
            player.src = '';
            modal.classList.add('hidden');
        }

        function copyUsername() {
            navigator.clipboard.writeText(currentData.username).then(() => {
                showToast('Username copied to clipboard!', 'success');
            }).catch(() => showToast('Failed to copy username', 'error'));
        }

        function showHelp() {
            document.getElementById('helpModal').classList.remove('hidden');
        }

        function closeHelpModal() {
            document.getElementById('helpModal').classList.add('hidden');
        }

        // Generic download handler
        async function downloadMedia(url, filename, useProxy = false) {
            if (!url) return showToast('No download link available', 'error');
            
            showToast('Preparing download...', 'info');
            
            try {
                const controller = new AbortController();
                setTimeout(() => controller.abort(), 45000);
                
                const response = await fetch(useProxy ? 'download.php' : url, {
                    method: useProxy ? 'POST' : 'GET',
                    headers: useProxy ? {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    } : {},
                    body: useProxy ? JSON.stringify({ url, filename, csrfToken }) : undefined,
                    signal: controller.signal
                });
                
                if (!response.ok) throw new Error('Download failed');
                
                const blob = await response.blob();
                const blobUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = blobUrl;
                a.download = filename.replace(/[^a-z0-9_\-\.]/gi, '_').substring(0, 100);
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                
                setTimeout(() => window.URL.revokeObjectURL(blobUrl), 5000);
                showToast('Download completed!', 'success');
            } catch (error) {
                showToast('Download failed, trying direct link...', 'warning');
                window.open(url, '_blank');
            }
        }

        // Enter key support
        document.getElementById('urlInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') getContent();
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            fetch('cleanup.php', {
                method: 'POST',
                headers: { 'X-CSRF-Token': csrfToken },
                keepalive: true
            }).catch(() => {});
        });
    </script>
</body>
</html>
