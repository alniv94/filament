<div>
    <div class="qr-code-full-container" style="max-width: 230px; margin: 0 auto;">
        <div class="flex justify-center qr-code-container">
            {!! $qrCode !!}
        </div>
        <div class="mt-2 text-center text-sm text-gray-500 flex flex-col">
           <span>{{ $record->equipment_number }}</span>
           <span>{{ $record->model_name }}</span>
        </div>
    </div>
    
    <div class="mt-3 flex justify-center">
        <button type="button" id="download-qr-btn" class="text-white bg-primary-600 hover:bg-primary-500 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download QR Code
        </button>
    </div>

    <script>
        document.getElementById('download-qr-btn').addEventListener('click', function() {
            const fullContainer = document.querySelector('.qr-code-full-container');
            if (!fullContainer) {
                console.error('QR code container not found');
                return;
            }

            const originalPadding = fullContainer.style.padding;
            const originalWidth = fullContainer.style.width;
            
            fullContainer.style.padding = '5px';
            fullContainer.style.backgroundColor = 'white';
            fullContainer.style.width = 'fit-content';
            
            function downloadUsingBlob(canvas, fileName) {
                canvas.toBlob(function(blob) {
                    if (blob) {
                        try {
                            const blobUrl = URL.createObjectURL(blob);
                            
                            const downloadLink = document.createElement('a');
                            downloadLink.href = blobUrl;
                            downloadLink.download = fileName;
                            downloadLink.style.display = 'none';
                            
                            document.body.appendChild(downloadLink);
                            downloadLink.click();
                            
                            setTimeout(function() {
                                document.body.removeChild(downloadLink);
                                URL.revokeObjectURL(blobUrl);
                            }, 100);
                        } catch (e) {
                            console.error('Error downloading QR code:', e);
                            fallbackDownload(canvas, fileName);
                        }
                    } else {
                        fallbackDownload(canvas, fileName);
                    }
                }, 'image/png');
            }
            
            function fallbackDownload(canvas, fileName) {
                try {
                    const pngDataURL = canvas.toDataURL('image/png');
                    const downloadLink = document.createElement('a');
                    downloadLink.href = pngDataURL;
                    downloadLink.download = fileName;
                    downloadLink.style.display = 'none';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                } catch (e) {
                    console.error('Fallback download also failed:', e);
                    alert('Unable to download QR code. Please try again or contact support.');
                }
            }
            
            const fileName = '{{ $record->equipment_number }}_{{ $record->model_name }}_qr.png'.replace(/\s+/g, '_');
            
            if (typeof html2canvas === 'function') {
                html2canvas(fullContainer, {
                    backgroundColor: 'white',
                    scale: 2,
                    width: fullContainer.offsetWidth,
                    useCORS: true
                }).then(function(canvas) {
                    downloadUsingBlob(canvas, fileName);
                    
                    fullContainer.style.padding = originalPadding;
                    fullContainer.style.width = originalWidth;
                    fullContainer.style.backgroundColor = '';
                });
            } else {
                // Fallback for browsers without html2canvas
                const qrContainer = document.querySelector('.qr-code-container');
                const textContainer = fullContainer.querySelector('.text-gray-500');
                
                const svgElement = qrContainer.querySelector('svg');
                if (!svgElement) {
                    console.error('QR code SVG not found');
                    return;
                }

                const svgRect = svgElement.getBoundingClientRect();
                
                const width = svgRect.width + 10;
                const height = svgRect.height + 50;
                
                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, width, height);
                
                const svgClone = svgElement.cloneNode(true);
                svgClone.setAttribute('width', svgRect.width);
                svgClone.setAttribute('height', svgRect.height);
                
                const svgData = new XMLSerializer().serializeToString(svgClone);
                const svgBase64 = btoa(unescape(encodeURIComponent(svgData)));
                const dataURL = 'data:image/svg+xml;base64,' + svgBase64;
                
                const img = new Image();
                img.onload = function() {
                    const qrX = (width - svgRect.width) / 2;
                    ctx.drawImage(img, qrX, 5, svgRect.width, svgRect.height);
                    
                    ctx.font = '12px Arial';
                    ctx.fillStyle = '#6B7280';
                    ctx.textAlign = 'center';
                    
                    const equipmentNumber = '{{ $record->equipment_number }}';
                    const modelName = '{{ $record->model_name }}';
                    
                    const textY = svgRect.height + 20;
                    ctx.fillText(equipmentNumber, width / 2, textY);
                    ctx.fillText(modelName, width / 2, textY + 15);
                    
                    downloadUsingBlob(canvas, fileName);
                };
                
                img.onerror = function() {
                    console.error('Error loading SVG image');
                    alert('Unable to generate QR code image. Please try again or contact support.');
                };
                
                img.src = dataURL;
                
                fullContainer.style.padding = originalPadding;
                fullContainer.style.width = originalWidth;
                fullContainer.style.backgroundColor = '';
            }
        });
    </script>
</div> 