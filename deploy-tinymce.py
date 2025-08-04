#!/usr/bin/env python3
import ftplib
import os
import zipfile
import requests
import tempfile

FTP_HOST = 'ftp.ipage.com'
FTP_USER = 'franko'
FTP_PASS = 'JyvR!HK2E!N55Zt'
REMOTE_DIR = '/11klassnikiru'

def upload_file(ftp, local_file, remote_file):
    """Upload a single file to FTP server"""
    try:
        with open(local_file, 'rb') as f:
            ftp.storbinary(f'STOR {remote_file}', f)
        print(f"✅ Uploaded: {remote_file}")
        return True
    except Exception as e:
        print(f"❌ Failed to upload {remote_file}: {str(e)}")
        return False

def upload_directory(ftp, local_dir, remote_dir):
    """Upload a directory recursively"""
    success_count = 0
    total_files = 0
    
    for root, dirs, files in os.walk(local_dir):
        # Create remote directories
        rel_path = os.path.relpath(root, local_dir)
        if rel_path != '.':
            remote_path = f"{remote_dir}/{rel_path}".replace('\\', '/')
            try:
                ftp.mkd(remote_path)
                print(f"📁 Created directory: {remote_path}")
            except:
                pass  # Directory might already exist
        
        # Upload files
        for file in files:
            total_files += 1
            local_file_path = os.path.join(root, file)
            if rel_path == '.':
                remote_file_path = f"{remote_dir}/{file}"
            else:
                remote_file_path = f"{remote_dir}/{rel_path}/{file}".replace('\\', '/')
            
            if upload_file(ftp, local_file_path, remote_file_path):
                success_count += 1
    
    return success_count, total_files

def download_tinymce():
    """Download TinyMCE if not present"""
    tinymce_dir = 'assets/js/tinymce'
    
    if os.path.exists(tinymce_dir):
        print(f"✅ TinyMCE directory already exists: {tinymce_dir}")
        return True
    
    print("📥 Downloading TinyMCE...")
    
    # Create assets/js directory
    os.makedirs('assets/js', exist_ok=True)
    
    # Download TinyMCE
    url = "https://download.tiny.cloud/tinymce/community/tinymce_7.6.0.zip"
    
    with tempfile.NamedTemporaryFile(delete=False, suffix='.zip') as tmp_file:
        response = requests.get(url, stream=True)
        response.raise_for_status()
        
        for chunk in response.iter_content(chunk_size=8192):
            tmp_file.write(chunk)
        
        tmp_zip_path = tmp_file.name
    
    # Extract TinyMCE
    with zipfile.ZipFile(tmp_zip_path, 'r') as zip_ref:
        zip_ref.extractall('assets/js/')
    
    # Clean up
    os.unlink(tmp_zip_path)
    
    # Download Russian language file
    ru_url = "https://www.tiny.cloud/docs/tinymce/7/ui-localization/"
    ru_file_path = f"{tinymce_dir}/js/tinymce/langs/ru.js"
    
    # Create langs directory
    os.makedirs(os.path.dirname(ru_file_path), exist_ok=True)
    
    # For now, create a basic Russian translation file
    ru_content = """tinymce.addI18n('ru', {
    "Redo": "Повторить",
    "Undo": "Отменить",
    "Cut": "Вырезать",
    "Copy": "Копировать",
    "Paste": "Вставить",
    "Select all": "Выделить всё",
    "New document": "Новый документ",
    "Ok": "ОК",
    "Cancel": "Отмена",
    "Visual aids": "Визуальные подсказки",
    "Bold": "Жирный",
    "Italic": "Курсив",
    "Underline": "Подчеркнутый",
    "Strikethrough": "Зачеркнутый",
    "Superscript": "Надстрочный",
    "Subscript": "Подстрочный",
    "Clear formatting": "Очистить форматирование",
    "Align left": "По левому краю",
    "Align center": "По центру",
    "Align right": "По правому краю",
    "Justify": "По ширине",
    "Bullet list": "Маркированный список",
    "Numbered list": "Нумерованный список",
    "Decrease indent": "Уменьшить отступ",
    "Increase indent": "Увеличить отступ",
    "Close": "Закрыть",
    "Formats": "Форматы",
    "Your browser doesn't support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.": "Ваш браузер не поддерживает прямой доступ к буферу обмена. Используйте сочетания клавиш Ctrl+X/C/V.",
    "Headers": "Заголовки",
    "Header 1": "Заголовок 1",
    "Header 2": "Заголовок 2",
    "Header 3": "Заголовок 3",
    "Header 4": "Заголовок 4",
    "Header 5": "Заголовок 5",
    "Header 6": "Заголовок 6",
    "Headings": "Заголовки",
    "Heading 1": "Заголовок 1",
    "Heading 2": "Заголовок 2",
    "Heading 3": "Заголовок 3",
    "Heading 4": "Заголовок 4",
    "Heading 5": "Заголовок 5",
    "Heading 6": "Заголовок 6",
    "Preformatted": "Предварительно отформатированный",
    "Div": "Div",
    "Pre": "Pre",
    "Code": "Код",
    "Paragraph": "Абзац",
    "Blockquote": "Цитата",
    "Inline": "Строчные",
    "Blocks": "Блоки",
    "Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.": "Вставка теперь в режиме обычного текста. Содержимое будет вставляться как обычный текст, пока вы не отключите эту опцию.",
    "Fonts": "Шрифты",
    "Font Sizes": "Размеры шрифта",
    "Class": "Класс",
    "Browse for an image": "Выбрать изображение",
    "OR": "ИЛИ",
    "Drop an image here": "Перетащите изображение сюда",
    "Upload": "Загрузить",
    "Block": "Блок",
    "Align": "Выравнивание",
    "Default": "По умолчанию",
    "Circle": "Круг",
    "Disc": "Диск",
    "Square": "Квадрат",
    "Lower Alpha": "Строчные буквы",
    "Lower Greek": "Строчные греческие",
    "Lower Roman": "Строчные римские",
    "Upper Alpha": "Прописные буквы",
    "Upper Roman": "Прописные римские",
    "Anchor...": "Якорь...",
    "Name": "Имя",
    "Id": "Идентификатор",
    "Id should start with a letter, followed only by letters, numbers, dashes, dots, colons or underscores.": "Идентификатор должен начинаться с буквы, за которой следуют только буквы, цифры, тире, точки, двоеточия или знаки подчеркивания.",
    "You have unsaved changes are you sure you want to navigate away?": "У вас есть несохраненные изменения. Вы уверены, что хотите покинуть страницу?",
    "Restore last draft": "Восстановить последний черновик",
    "Special character...": "Специальный символ...",
    "Source code": "Исходный код",
    "Insert/Edit code sample": "Вставить/редактировать образец кода",
    "Language": "Язык",
    "Code sample...": "Образец кода...",
    "Color Picker": "Выбор цвета",
    "R": "К",
    "G": "З",
    "B": "С",
    "Left to right": "Слева направо",
    "Right to left": "Справа налево",
    "Emoticons": "Смайлики",
    "Emoticons...": "Смайлики...",
    "Metadata and Document Properties": "Метаданные и свойства документа",
    "Title": "Заголовок",
    "Keywords": "Ключевые слова",
    "Description": "Описание",
    "Robots": "Роботы",
    "Author": "Автор",
    "Encoding": "Кодировка",
    "Fullscreen": "Полный экран",
    "Action": "Действие",
    "Shortcut": "Горячая клавиша",
    "Help": "Помощь",
    "Address": "Адрес",
    "Focus to menubar": "Фокус на панель меню",
    "Focus to toolbar": "Фокус на панель инструментов",
    "Focus to element path": "Фокус на путь элемента",
    "Focus to contextual toolbar": "Фокус на контекстную панель инструментов",
    "Insert link (if link plugin activated)": "Вставить ссылку (если плагин ссылок активирован)",
    "Save (if save plugin activated)": "Сохранить (если плагин сохранения активирован)",
    "Find (if searchreplace plugin activated)": "Найти (если плагин поиска и замены активирован)",
    "Plugins installed ({0}):": "Установленные плагины ({0}):",
    "Premium plugins:": "Премиум плагины:",
    "Learn more...": "Узнать больше...",
    "You are using {0}": "Вы используете {0}",
    "Plugins": "Плагины",
    "Handy Shortcuts": "Полезные сочетания клавиш",
    "Horizontal line": "Горизонтальная линия",
    "Insert/edit image": "Вставить/редактировать изображение",
    "Alternative description": "Альтернативное описание",
    "Accessibility": "Доступность",
    "Image is decorative": "Изображение декоративное",
    "Source": "Источник",
    "Dimensions": "Размеры",
    "Constrain proportions": "Сохранить пропорции",
    "General": "Общие",
    "Advanced": "Дополнительно",
    "Style": "Стиль",
    "Vertical space": "Вертикальный отступ",
    "Horizontal space": "Горизонтальный отступ",
    "Border": "Граница",
    "Insert image": "Вставить изображение",
    "Image...": "Изображение...",
    "Image list": "Список изображений",
    "Rotate counterclockwise": "Повернуть против часовой стрелки",
    "Rotate clockwise": "Повернуть по часовой стрелке",
    "Flip vertically": "Отразить по вертикали",
    "Flip horizontally": "Отразить по горизонтали",
    "Edit image": "Редактировать изображение",
    "Image options": "Настройки изображения",
    "Zoom in": "Увеличить",
    "Zoom out": "Уменьшить",
    "Crop": "Обрезать",
    "Resize": "Изменить размер",
    "Orientation": "Ориентация",
    "Brightness": "Яркость",
    "Sharpen": "Резкость",
    "Contrast": "Контрастность",
    "Color levels": "Уровни цветов",
    "Gamma": "Гамма",
    "Invert": "Инвертировать",
    "Apply": "Применить",
    "Back": "Назад",
    "Insert date/time": "Вставить дату/время",
    "Date/time": "Дата/время",
    "Insert/edit link": "Вставить/редактировать ссылку",
    "Text to display": "Отображаемый текст",
    "Url": "URL",
    "Open link in...": "Открыть ссылку в...",
    "Current window": "Текущем окне",
    "None": "Нет",
    "New window": "Новом окне",
    "Open link": "Открыть ссылку",
    "Remove link": "Удалить ссылку",
    "Anchors": "Якоря",
    "Link...": "Ссылка...",
    "Paste or type a link": "Вставьте или введите ссылку",
    "The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?": "Введенный URL похож на адрес электронной почты. Хотите добавить необходимый префикс mailto:?",
    "The URL you entered seems to be an external link. Do you want to add the required http:// prefix?": "Введенный URL похож на внешнюю ссылку. Хотите добавить необходимый префикс http://?",
    "The URL you entered seems to be an external link. Do you want to add the required https:// prefix?": "Введенный URL похож на внешнюю ссылку. Хотите добавить необходимый префикс https://?",
    "Link list": "Список ссылок",
    "Insert video": "Вставить видео",
    "Insert/edit video": "Вставить/редактировать видео",
    "Insert/edit media": "Вставить/редактировать медиа",
    "Alternative source": "Альтернативный источник",
    "Alternative source URL": "URL альтернативного источника",
    "Media poster (Image URL)": "Постер медиа (URL изображения)",
    "Paste your embed code below:": "Вставьте код встраивания ниже:",
    "Embed": "Встроить",
    "Media...": "Медиа...",
    "Nonbreaking space": "Неразрывный пробел",
    "Page break": "Разрыв страницы",
    "Paste as text": "Вставить как текст",
    "Preview": "Предварительный просмотр",
    "Print...": "Печать...",
    "Save": "Сохранить",
    "Find": "Найти",
    "Replace with": "Заменить на",
    "Replace": "Заменить",
    "Replace all": "Заменить все",
    "Previous": "Предыдущий",
    "Next": "Следующий",
    "Find and Replace": "Найти и заменить",
    "Find and replace...": "Найти и заменить...",
    "Could not find the specified string.": "Не удалось найти указанную строку.",
    "Match case": "Учитывать регистр",
    "Find whole words only": "Найти только целые слова",
    "Find in selection": "Найти в выделении",
    "Spellcheck": "Проверка орфографии",
    "Spellcheck Language": "Язык проверки орфографии",
    "No misspellings found.": "Орфографические ошибки не найдены.",
    "Ignore": "Игнорировать",
    "Ignore all": "Игнорировать все",
    "Finish": "Завершить",
    "Add to Dictionary": "Добавить в словарь",
    "Insert table": "Вставить таблицу",
    "Table properties": "Свойства таблицы",
    "Delete table": "Удалить таблицу",
    "Cell": "Ячейка",
    "Row": "Строка",
    "Column": "Столбец",
    "Cell properties": "Свойства ячейки",
    "Merge cells": "Объединить ячейки",
    "Split cell": "Разделить ячейку",
    "Insert row before": "Вставить строку выше",
    "Insert row after": "Вставить строку ниже",
    "Delete row": "Удалить строку",
    "Row properties": "Свойства строки",
    "Cut row": "Вырезать строку",
    "Copy row": "Копировать строку",
    "Paste row before": "Вставить строку выше",
    "Paste row after": "Вставить строку ниже",
    "Insert column before": "Вставить столбец слева",
    "Insert column after": "Вставить столбец справа",
    "Delete column": "Удалить столбец",
    "Cols": "Столбцы",
    "Rows": "Строки",
    "Width": "Ширина",
    "Height": "Высота",
    "Cell spacing": "Расстояние между ячейками",
    "Cell padding": "Отступ в ячейках",
    "Caption": "Заголовок",
    "Show caption": "Показать заголовок",
    "Left": "Слева",
    "Center": "По центру",
    "Right": "Справа",
    "Cell type": "Тип ячейки",
    "Scope": "Область",
    "Alignment": "Выравнивание",
    "H Align": "Горизонтальное выравнивание",
    "V Align": "Вертикальное выравнивание",
    "Top": "Сверху",
    "Middle": "По середине",
    "Bottom": "Снизу",
    "Header cell": "Ячейка заголовка",
    "Row group": "Группа строк",
    "Column group": "Группа столбцов",
    "Row type": "Тип строки",
    "Header": "Заголовок",
    "Body": "Тело",
    "Footer": "Подвал",
    "Border color": "Цвет границы",
    "Insert template...": "Вставить шаблон...",
    "Templates": "Шаблоны",
    "Template": "Шаблон",
    "Text color": "Цвет текста",
    "Background color": "Цвет фона",
    "Custom...": "Пользовательский...",
    "Custom color": "Пользовательский цвет",
    "No color": "Без цвета",
    "Remove color": "Удалить цвет",
    "Table of Contents": "Содержание",
    "Show blocks": "Показать блоки",
    "Show invisible characters": "Показать невидимые символы",
    "Word count": "Количество слов",
    "Count": "Подсчет",
    "Document": "Документ",
    "Selection": "Выделение",
    "Words": "Слова",
    "Words: {0}": "Слова: {0}",
    "{0} words": "{0} слов",
    "File": "Файл",
    "Edit": "Редактировать",
    "Insert": "Вставить",
    "View": "Вид",
    "Format": "Формат",
    "Table": "Таблица",
    "Tools": "Инструменты",
    "Powered by {0}": "Работает на {0}",
    "Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help": "Область форматированного текста. Нажмите ALT-F9 для меню. Нажмите ALT-F10 для панели инструментов. Нажмите ALT-0 для справки",
    "Image title": "Заголовок изображения",
    "Border width": "Ширина границы",
    "Border style": "Стиль границы",
    "Error": "Ошибка",
    "Warn": "Предупреждение",
    "Valid": "Действительный",
    "To open the popup, press Shift+Enter": "Чтобы открыть всплывающее окно, нажмите Shift+Enter",
    "Rich Text Area": "Область форматированного текста",
    "Rich Text Area. Press ALT-0 for help.": "Область форматированного текста. Нажмите ALT-0 для справки.",
    "System Font": "Системный шрифт",
    "Failed to upload image: {0}": "Не удалось загрузить изображение: {0}",
    "Failed to load plugin: {0} from url {1}": "Не удалось загрузить плагин: {0} с URL {1}",
    "Failed to load plugin url: {0}": "Не удалось загрузить URL плагина: {0}",
    "Failed to initialize plugin: {0}": "Не удалось инициализировать плагин: {0}",
    "example": "пример",
    "Search": "Поиск",
    "All": "Все",
    "Currency": "Валюта",
    "Text": "Текст",
    "Quotations": "Кавычки",
    "Mathematical": "Математические",
    "Extended Latin": "Расширенная латиница",
    "Symbols": "Символы",
    "Arrows": "Стрелки",
    "User Defined": "Определенные пользователем",
    "dollar sign": "знак доллара",
    "currency sign": "знак валюты",
    "euro-currency sign": "знак евро",
    "colon sign": "знак двоеточия",
    "cruzeiro sign": "знак крузейро",
    "french franc sign": "знак французского франка",
    "lira sign": "знак лиры",
    "mill sign": "знак милля",
    "naira sign": "знак найры",
    "peseta sign": "знак песеты",
    "rupee sign": "знак рупии",
    "won sign": "знак воны",
    "new sheqel sign": "знак нового шекеля",
    "dong sign": "знак донга",
    "kip sign": "знак кипа",
    "tugrik sign": "знак тугрика",
    "drachma sign": "знак драхмы",
    "german penny symbol": "символ немецкого пенни",
    "peso sign": "знак песо",
    "guarani sign": "знак гуарани",
    "austral sign": "знак аустрала",
    "hryvnia sign": "знак гривны",
    "cedi sign": "знак седи",
    "livre tournois sign": "знак турского ливра",
    "spesmilo sign": "знак спесмило",
    "tenge sign": "знак тенге",
    "indian rupee sign": "знак индийской рупии",
    "turkish lira sign": "знак турецкой лиры",
    "nordic mark sign": "знак северной марки",
    "manat sign": "знак маната",
    "ruble sign": "знак рубля",
    "yen character": "символ иены",
    "yuan character": "символ юаня",
    "yuan character, in hong kong and taiwan": "символ юаня в Гонконге и Тайване",
    "yen/yuan character variant one": "вариант символа иены/юаня один",
    "Loading...": "Загрузка...",
    "Get Index": "Получить индекс",
    "New Line": "Новая строка",
    "Line Height": "Высота строки",
    "Bulleted list": "Маркированный список",
    "Clipboard": "Буфер обмена",
    "Dropped file type is not supported": "Тип перетащенного файла не поддерживается",
    "Edit Html": "Редактировать HTML",
    "Dropped files type is not supported": "Тип перетащенных файлов не поддерживается"
});
"""
    
    with open(ru_file_path, 'w', encoding='utf-8') as f:
        f.write(ru_content)
    
    print(f"✅ TinyMCE downloaded and Russian language file created")
    return True

try:
    print("🚀 Deploying TinyMCE Rich Text Editor")
    print("=====================================")
    
    # Download TinyMCE if needed
    if not download_tinymce():
        raise Exception("Failed to download TinyMCE")
    
    # Connect to FTP
    ftp = ftplib.FTP(FTP_HOST)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.cwd(REMOTE_DIR)
    
    print("\n📁 Creating remote directories...")
    
    # Create assets directory structure
    try:
        ftp.mkd('assets')
    except:
        pass
    
    try:
        ftp.mkd('assets/js')
    except:
        pass
    
    try:
        ftp.mkd('uploads')
    except:
        pass
    
    try:
        ftp.mkd('uploads/images')
    except:
        pass
    
    print("\n📤 Uploading TinyMCE files...")
    
    # Upload TinyMCE directory
    tinymce_success, tinymce_total = upload_directory(ftp, 'assets/js/tinymce', 'assets/js/tinymce')
    
    print(f"\n📤 Uploading dashboard and handler files...")
    
    # Files to upload
    files_to_upload = {
        'dashboard-create-content-unified.php': 'dashboard-create-content-unified.php',
        'upload-image.php': 'upload-image.php'
    }
    
    php_success = 0
    php_total = len(files_to_upload)
    
    for local_file, remote_file in files_to_upload.items():
        if os.path.exists(local_file):
            if upload_file(ftp, local_file, remote_file):
                php_success += 1
        else:
            print(f"⚠️  Local file not found: {local_file}")
    
    ftp.quit()
    
    total_success = tinymce_success + php_success
    total_files = tinymce_total + php_total
    
    print(f"\n📊 Deployment Summary:")
    print(f"   - TinyMCE files: {tinymce_success}/{tinymce_total}")
    print(f"   - PHP files: {php_success}/{php_total}")
    print(f"   - Total success: {total_success}/{total_files}")
    
    if total_success == total_files:
        print("\n✅ TinyMCE Rich Text Editor deployed successfully!")
        
        print("\n🎉 What's New:")
        print("   - ✅ Self-hosted TinyMCE 7.6.0 (no cloud dependencies)")
        print("   - ✅ Russian language interface")
        print("   - ✅ Image upload with drag & drop support")
        print("   - ✅ Rich text formatting (bold, italic, lists, etc.)")
        print("   - ✅ Clean responsive toolbar")
        print("   - ✅ Secure file upload validation")
        
        print("\n📝 Features Available:")
        print("   - Bold, italic, underline, strikethrough")
        print("   - Headers (H1-H6), paragraphs, blockquotes")
        print("   - Bullet and numbered lists")
        print("   - Text alignment (left, center, right, justify)")
        print("   - Image upload with drag & drop")
        print("   - Links with target options")
        print("   - Tables with full editing support")
        print("   - Code blocks and syntax highlighting")
        print("   - Find & replace functionality")
        print("   - Fullscreen editing mode")
        
        print("\n🔗 Test the Rich Text Editor:")
        print("Create News: https://11klassniki.ru/create/news")
        print("Create Post: https://11klassniki.ru/create/post")
        
        print("\n🖼️ Image Upload:")
        print("   - Max file size: 5MB")
        print("   - Supported formats: JPEG, PNG, GIF, WebP")
        print("   - Drag & drop or click to browse")
        print("   - Secure admin-only access")
        print("   - Files saved to /uploads/images/")
        
        print("\n💡 Usage Tips:")
        print("   - Drag images directly into the editor")
        print("   - Use Ctrl+K for quick link insertion")
        print("   - Press F11 for fullscreen editing")
        print("   - Content auto-saves as you type")
        
    else:
        print(f"\n⚠️  Some files failed to upload ({total_files - total_success} failed)")
        print("Please check the errors above and retry if needed.")
    
except Exception as e:
    print(f"\n❌ Deployment Error: {str(e)}")