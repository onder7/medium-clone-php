<?php 
include "core/init.php";
protect_page();
?>
<!DOCTYPE HTML>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<html class="screen-scroll">
<head>
    <title>Broomble - Yeni Gönderi</title>
    <link href="css/new-post.css" rel="stylesheet" type="text/css" media="screen">
    <script src="js/jquery.min.js"></script>
</head>

<?php $prop = md5(time()); ?>

<body>
    <div class="site-nav-overlay"></div>
    <button class="site-nav-logo"><span class="icons icons-logo-m"></span></button>
    
    <div class="container" id="container"> 
        <div class="screen-content" id="prerendered">
            <article class="post-article grid-breaking">
                <div class="metabar active">
                    <section class="metabar-status">
                        <span class="metabar-message"></span>
                        <span class="metabar-error"></span>
                    </section>
                    <div class="metabar-actions metabar-mode-edit">
                        <ul class="metabar-actions-btns">   
                            <li>
                                <button title="Taslak Kaydet" class="btn btn-small" data-action="save-draft">Taslak Kaydet</button>
                            </li>
                            <li>
                                <button title="Yayınla" class="btn btn-primary btn-small btn-publish" data-action="publish">Yayınla</button>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <section class="post-page-wrapper post-page-wrapper-contain">  
                    <div class="post-content">
                        <div class="post-content-inner">
                            <div class="notes-source">
                                <header class="post-header post-header-headline">
                                    <h1 class="post-title editable default-value" 
                                        id="post_title_<?=$prop?>" 
                                        name="title" 
                                        itemprop="name" 
                                        g_editable="true" 
                                        role="textbox" 
                                        contenteditable="true" 
                                        data-placeholder="Başlık yazın"></h1>
                                    
                                    <h2 class="post-field subtitle editable default-value" 
                                        name="subtitle" 
                                        g_editable="true" 
                                        role="textbox" 
                                        contenteditable="true" 
                                        data-placeholder="Alt başlık yazın (opsiyonel)"></h2>
                                    
                                    <div class="post-field content editable default-value" 
                                         name="content" 
                                         g_editable="true" 
                                         role="textbox" 
                                         contenteditable="true" 
                                         data-placeholder="Gönderinizi yazın"></div>
                                </header>
                            </div>
                        </div>
                        <div class="post-follow-ups post-supplemental"></div>
                    </div>
                </section>
            </article>
        </div>
    </div>

    <link rel="stylesheet" href="css/medium.editor.css">
    <script src="js/medium.editor.js"></script>
    
    <script>
    $(document).ready(function() {
        // Medium Editor'ü başlat
        var editor = new MediumEditor('.editable');
        
        // Maksimum karakter limitleri
        const MAX_TITLE_LENGTH = 200;
        const MAX_SUBTITLE_LENGTH = 150;
        const MAX_CONTENT_LENGTH = 50000;
        
        // Form verilerini hazırla ve doğrula
        function preparePostData() {
            let post_title = $.trim($('#post_title_<?=$prop?>').text());
            let post_sub = $.trim($('.subtitle').text());
            let post_data = $.trim($('.content').html());
            
            // Başlık kontrolü
            if(post_title === "") {
                showError("Lütfen bir başlık girin");
                return false;
            }
            if(post_title.length > MAX_TITLE_LENGTH) {
                showError(`Başlık ${MAX_TITLE_LENGTH} karakterden uzun olamaz`);
                return false;
            }
            
            // Alt başlık kontrolü
            if(post_sub.length > MAX_SUBTITLE_LENGTH) {
                showError(`Alt başlık ${MAX_SUBTITLE_LENGTH} karakterden uzun olamaz`);
                return false;
            }
            
            // İçerik kontrolü
            if(post_data === "") {
                showError("Lütfen içerik girin");
                return false;
            }
            if(post_data.length > MAX_CONTENT_LENGTH) {
                showError(`İçerik ${MAX_CONTENT_LENGTH} karakterden uzun olamaz`);
                return false;
            }
            
            return {
                post_title: encodeURIComponent(post_title),
                post_sub: encodeURIComponent(post_sub),
                post_data: encodeURIComponent(post_data)
            };
        }
        
        // Hata mesajı göster
        function showError(message) {
            $(".metabar-error").text(message).show();
            $(".metabar-message").hide();
            setTimeout(() => {
                $(".metabar-error").fadeOut();
            }, 3000);
        }
        
        // Durum mesajı göster
        function showStatus(message) {
            $(".metabar-message").text(message).show();
            $(".metabar-error").hide();
        }
        
        // Butonları devre dışı bırak/etkinleştir
        function toggleButtons(disabled) {
            $('button[data-action="save-draft"], button[data-action="publish"]')
                .prop('disabled', disabled);
        }
        
        // AJAX post işlemi
        function submitPost(data, isDraft) {
            toggleButtons(true);
            showStatus(isDraft ? 'Kaydediliyor...' : 'Yayınlanıyor...');
            
            $.ajax({
                type: "POST",
                url: "ajax/post_p.php",
                data: {
                    ...data,
                    post_view: isDraft ? 0 : 1
                },
                cache: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if(result.success) {
                            showStatus(isDraft ? 'Kaydedildi' : 'Yayınlandı');
                            setTimeout(() => {
                                window.location = "index.php";
                            }, 1000);
                        } else {
                            showError(result.message || 'Bir hata oluştu');
                            toggleButtons(false);
                        }
                    } catch(e) {
                        showError('Bir hata oluştu');
                        toggleButtons(false);
                    }
                },
                error: function() {
                    showError('Bağlantı hatası');
                    toggleButtons(false);
                }
            });
        }
        
        // Buton tıklama olaylarını dinle
        $('button').click(function(e) {
            const action = $(this).data("action");
            const postData = preparePostData();
            
            if(!postData) return;
            
            switch(action) {
                case 'save-draft':
                    submitPost(postData, true);
                    break;
                    
                case 'publish':
                    submitPost(postData, false);
                    break;
            }
        });
        
        // Otomatik kaydetme (her 60 saniyede)
        let autoSaveTimer = setInterval(() => {
            const postData = preparePostData();
            if(postData && postData.post_title !== "") {
                submitPost(postData, true);
            }
        }, 60000);
        
        // Sayfa kapatılırken uyar
        window.onbeforeunload = function() {
            const postData = preparePostData();
            if(postData && postData.post_title !== "") {
                return "Kaydedilmemiş değişiklikleriniz var. Çıkmak istediğinizden emin misiniz?";
            }
        };
    });
    </script>
</body>
</html>