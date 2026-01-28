# smart_wardrobe_simple_bot.py
import telebot
import requests
from bs4 import BeautifulSoup
import json
import random
import time
import re
from telebot import types
import logging
import urllib.parse
from urllib.parse import quote

print("="*60)
print("🛍️ SMART WARDROBE BOT - REAL PRODUCTS (SIMPLE VERSION)")
print("="*60)

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Get token
BOT_TOKEN = input("Enter your bot token: ").strip()

# Create bot
bot = telebot.TeleBot(BOT_TOKEN)

# ========== ALL THEMES AND OCCASIONS ==========
THEMES = {
    
    # ADD THESE 10 NEW THEMES:
    "smartcasual": {
        "display_name": "👖 Smart Casual",
        "description": "Polished yet comfortable outfits",
        "emoji": "👖",
        "zalora_keywords": ["chinos", "polo shirt", "blouse", "loafers", "smart casual"],
        "occasions": ["Date", "Lunch Meeting", "Casual Friday", "Event", "Brunch"]
    },
    "minimalist": {
        "display_name": "⚫ Minimalist Style",
        "description": "Simple and monochrome outfits",
        "emoji": "⚫",
        "zalora_keywords": ["basic tee", "plain shirt", "minimalist", "neutral", "simple"],
        "occasions": ["Minimalist Event", "Gallery", "Coffee Date", "Simple Gathering", "Office"]
    },
    "vintage": {
        "display_name": "📺 Vintage Retro",
        "description": "Retro and classic fashion",
        "emoji": "📺",
        "zalora_keywords": ["vintage dress", "retro shirt", "classic", "old school", "retro"],
        "occasions": ["Retro Party", "Vintage Fair", "Theme Event", "Classic Dinner", "Concert"]
    },
    "beach": {
        "display_name": "🏖️ Beach & Vacation",
        "description": "Summer and holiday outfits",
        "emoji": "🏖️",
        "zalora_keywords": ["swimwear", "beach dress", "sandals", "cover up", "summer"],
        "occasions": ["Beach Day", "Pool Party", "Island Vacation", "Summer Festival", "Holiday"]
    },
    "winter": {
        "display_name": "❄️ Winter & Cold Weather",
        "description": "Warm clothing for cold days",
        "emoji": "❄️",
        "zalora_keywords": ["jacket", "sweater", "coat", "thermal", "winter"],
        "occasions": ["Winter Trip", "Snow Holiday", "Cold Season", "Mountain Trip", "Skiing"]
    },
    "ethnic": {
        "display_name": "🎎 Traditional & Ethnic",
        "description": "Cultural and traditional wear",
        "emoji": "🎎",
        "zalora_keywords": ["baju kurung", "kebaya", "traditional", "ethnic", "cultural"],
        "occasions": ["Wedding", "Hari Raya", "Cultural Event", "Festival", "Traditional Ceremony"]
    },
    "workout": {
        "display_name": "💪 Fitness & Training",
        "description": "Exercise and fitness gear",
        "emoji": "💪",
        "zalora_keywords": ["gym shorts", "sports bra", "training", "fitness", "workout"],
        "occasions": ["Workout", "Training", "Fitness Class", "Marathon", "Gym Session"]
    },
    "wedding": {
        "display_name": "💒 Wedding Attire",
        "description": "Wedding outfits and formal wear",
        "emoji": "💒",
        "zalora_keywords": ["wedding dress", "suit", "formal gown", "evening dress", "wedding"],
        "occasions": ["Wedding", "Engagement", "Bridal Shower", "Anniversary", "Formal Dinner"]
    },
    "travel": {
        "display_name": "✈️ Travel Comfort",
        "description": "Comfortable travel outfits",
        "emoji": "✈️",
        "zalora_keywords": ["travel pants", "comfort wear", "jacket", "backpack", "travel"],
        "occasions": ["Flight", "Road Trip", "Backpacking", "Travel", "Vacation"]
    },
    "business": {
        "display_name": "💼 Business Professional",
        "description": "Corporate business attire",
        "emoji": "💼",
        "zalora_keywords": ["business suit", "office dress", "corporate", "professional", "business"],
        "occasions": ["Business Meeting", "Corporate Event", "Presentation", "Networking", "Conference"]
    },
    "evening": {
        "display_name": "🌙 Evening Elegance",
        "description": "Sophisticated evening wear",
        "emoji": "🌙",
        "zalora_keywords": ["evening dress", "cocktail dress", "heels", "evening gown", "elegant"],
        "occasions": ["Gala Dinner", "Awards Night", "Theater", "Fine Dining", "Special Event"]
    },
    "outdoor": {
        "display_name": "🌲 Outdoor Adventure",
        "description": "Outdoor and adventure clothing",
        "emoji": "🌲",
        "zalora_keywords": ["hiking", "outdoor", "adventure", "camping", "trekking"],
        "occasions": ["Hiking", "Camping", "Adventure Trip", "Nature Walk", "Outdoor Activity"]
    },
    "casual": {
        "display_name": "👕 Casual Wear",
        "description": "Everyday comfortable outfits",
        "emoji": "👕",
        "zalora_keywords": ["t-shirt", "jeans", "hoodie", "sneakers", "casual"],
        "occasions": ["Hangout", "Brunch", "Shopping", "Campus"]
    },
    "formal": {
        "display_name": "👔 Formal Attire", 
        "description": "Professional and office wear",
        "emoji": "👔",
        "zalora_keywords": ["shirt", "blazer", "dress pants", "formal"],
        "occasions": ["Office", "Meeting", "Interview", "Conference"]
    },
    "sporty": {
        "display_name": "🏃 Sporty Activewear",
        "description": "Workout and sports clothing",
        "emoji": "🏃",
        "zalora_keywords": ["sportswear", "activewear", "running", "gym"],
        "occasions": ["Gym", "Running", "Sports", "Yoga"]
    },
    "party": {
        "display_name": "🎉 Party & Night Out",
        "description": "Night out and celebration wear",
        "emoji": "🎉",
        "zalora_keywords": ["party", "dress", "evening", "celebration"],
        "occasions": ["Birthday", "Club", "Dinner Date", "Celebration"]
    },
    "streetwear": {
        "display_name": "🛹 Street Style",
        "description": "Urban fashion trends",
        "emoji": "🛹",
        "zalora_keywords": ["hoodie", "streetwear", "cargo", "urban"],
        "occasions": ["Street Photography", "Concert", "Urban Event", "Fashion Show"]
    }
}

class RealZaloraScraper:
    """Real Zalora Malaysia scraper - SIMPLE VERSION"""
    
    def __init__(self):
        self.base_url = "https://www.zalora.com.my"
        self.session = requests.Session()
        self.session.headers.update({
            "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Language": "en-US,en;q=0.9,ms;q=0.8",
            "Accept-Encoding": "gzip, deflate, br",
            "Connection": "keep-alive",
        })
        
    def get_real_products(self, theme, limit=4):
        """Get REAL products from Zalora"""
        if theme not in THEMES:
            return []
        
        theme_data = THEMES[theme]
        logger.info(f"Getting REAL products for: {theme_data['display_name']}")
        
        # Try each keyword
        all_products = []
        for keyword in theme_data['zalora_keywords'][:3]:
            try:
                products = self._search_zalora(keyword, limit=2)
                if products:
                    for p in products:
                        p['theme'] = theme
                        p['theme_name'] = theme_data['display_name']
                    all_products.extend(products)
                    time.sleep(1)  # Be polite
            except Exception as e:
                logger.warning(f"Keyword '{keyword}' failed: {e}")
                continue
        
        # Remove duplicates
        seen = set()
        unique = []
        for p in all_products:
            if p.get('name') and p['name'] not in seen:
                seen.add(p['name'])
                unique.append(p)
        
        return unique[:limit]
    
    def _search_zalora(self, keyword, limit=3):
        """Search Zalora and parse results"""
        try:
            # Create search URL
            encoded_keyword = quote(keyword)
            url = f"{self.base_url}/catalog/?q={encoded_keyword}"
            
            logger.info(f"Searching Zalora: {url}")
            
            # Send request
            response = self.session.get(url, timeout=15)
            response.raise_for_status()
            
            if response.status_code != 200:
                return []
            
            # Parse HTML
            soup = BeautifulSoup(response.content, 'html.parser')
            
            # Find ALL product containers - try multiple selectors
            products = []
            
            # Method 1: Look for product cards
            product_cards = soup.find_all('div', {'data-testid': 'product-card'})
            
            # Method 2: Look for product links
            if not product_cards:
                product_cards = soup.find_all('a', href=lambda x: x and '/product/' in x)
            
            # Method 3: Look for any product-like divs
            if not product_cards:
                product_cards = soup.find_all('div', class_=re.compile(r'(product|item|card)'))
            
            for element in product_cards[:limit*2]:  # Get extra for filtering
                try:
                    product = self._extract_product(element)
                    if product and product.get('name'):
                        products.append(product)
                except Exception as e:
                    continue
            
            return products[:limit]
            
        except Exception as e:
            logger.error(f"Search error: {e}")
            return []
    
    def _extract_product(self, element):
        """Extract product info from HTML element"""
        try:
            # Get product name
            name = None
            
            # Try different ways to find name
            name_selectors = [
                element.find('h3'),
                element.find('div', class_=re.compile(r'(name|title)')),
                element.find('span', class_=re.compile(r'(name|title)')),
            ]
            
            for elem in name_selectors:
                if elem and elem.text.strip():
                    name = elem.text.strip()
                    break
            
            if not name:
                # Try getting from data attributes
                if element.has_attr('data-name'):
                    name = element['data-name']
                elif element.has_attr('aria-label'):
                    name = element['aria-label']
            
            if not name:
                return None
            
            # Get price
            price = "Check Price"
            price_selectors = [
                element.find('span', class_=re.compile(r'(price|amount)')),
                element.find('div', class_=re.compile(r'price')),
                element.find('span', {'data-testid': 'product-price'}),
            ]
            
            for elem in price_selectors:
                if elem and elem.text.strip():
                    price_text = elem.text.strip()
                    # Clean price text
                    price_match = re.search(r'RM\s?\d+[\d,\.]*', price_text)
                    if price_match:
                        price = price_match.group(0)
                    else:
                        price = price_text[:30]
                    break
            
            # Get image
            image = ""
            img_elem = element.find('img')
            if img_elem:
                img_src = img_elem.get('src') or img_elem.get('data-src', '')
                if img_src:
                    if img_src.startswith('//'):
                        image = f"https:{img_src}"
                    elif img_src.startswith('http'):
                        image = img_src
                    elif img_src.startswith('/'):
                        image = f"{self.base_url}{img_src}"
            
            # Get link
            link = f"{self.base_url}/catalog/?q={quote(name.split()[0])}"
            
            # Try to find actual product link
            link_elem = element.find('a', href=True)
            if link_elem:
                href = link_elem['href']
                if href:
                    if href.startswith('//'):
                        link = f"https:{href}"
                    elif href.startswith('http'):
                        link = href
                    elif href.startswith('/'):
                        link = f"{self.base_url}{href}"
            
            return {
                "name": name[:80] + "..." if len(name) > 80 else name,
                "price": price,
                "image": image,
                "link": link,
                "source": "Zalora Malaysia"
            }
            
        except Exception as e:
            logger.debug(f"Extract error: {e}")
            return None
    
    def search_direct(self, query):
        """Get direct search link"""
        encoded_query = quote(query)
        return f"{self.base_url}/catalog/?q={encoded_query}"

# Initialize scraper
scraper = RealZaloraScraper()

# ========== BOT COMMANDS ==========
@bot.message_handler(commands=['start'])
def send_welcome(message):
    """Send welcome message"""
    welcome_msg = """
    🛍️ *SMART WARDROBE BOT - REAL PRODUCTS* 🛍️
    
    *✨ GET REAL ZALORA PRODUCTS!*
    
    *How it works:*
    1. Choose a fashion theme
    2. Bot searches Zalora Malaysia LIVE
    3. Get ACTUAL products with prices
    4. Click links to shop directly
    
    *🎯 Available Themes:*
    • 👕 Casual Wear
    • 👔 Formal Attire  
    • 🏃 Sporty Activewear
    • 🎉 Party & Night Out
    • 🛹 Street Style
    
    *Ready to shop REAL fashion?* 👇
    """
    
    markup = types.ReplyKeyboardMarkup(resize_keyboard=True, row_width=2)
    btn1 = types.KeyboardButton("🎨 BROWSE ALL THEMES")
    btn2 = types.KeyboardButton("✨ GET RECOMMENDATION")
    btn3 = types.KeyboardButton("🔍 SEARCH PRODUCTS")
    btn4 = types.KeyboardButton("📋 HOW TO USE")
    markup.add(btn1, btn2, btn3, btn4)
    
    bot.send_message(
        message.chat.id,
        welcome_msg,
        parse_mode="Markdown",
        reply_markup=markup
    )

def send_all_themes(chat_id):
    """Send all themes"""
    text = "🎨 *CHOOSE YOUR STYLE*\n\n*Select a theme for REAL Zalora products:*"
    
    markup = types.InlineKeyboardMarkup(row_width=2)
    
    for theme_key, theme_data in THEMES.items():
        btn_text = f"{theme_data['emoji']} {theme_data['display_name'].split()[0]}"
        markup.add(
            types.InlineKeyboardButton(btn_text, callback_data=f"theme_{theme_key}")
        )
    
    markup.row(
        types.InlineKeyboardButton("🔍 DIRECT ZALORA SEARCH", callback_data="direct_zalora")
    )
    
    bot.send_message(
        chat_id,
        text,
        parse_mode="Markdown",
        reply_markup=markup
    )

@bot.callback_query_handler(func=lambda call: call.data.startswith("theme_"))
def handle_theme_selection(call):
    """Handle theme selection"""
    theme_key = call.data.split("_")[1]
    
    if theme_key not in THEMES:
        bot.answer_callback_query(call.id, "Invalid theme")
        return
    
    theme_data = THEMES[theme_key]
    
    # Show theme info
    theme_msg = (
        f"{theme_data['emoji']} *{theme_data['display_name']}*\n\n"
        f"*Description:* {theme_data['description']}\n\n"
        f"*🎯 Perfect For:*\n"
    )
    
    for occasion in theme_data["occasions"]:
        theme_msg += f"• {occasion}\n"
    
    theme_msg += f"\n*🔍 Searching REAL products on Zalora...*"
    
    try:
        bot.edit_message_text(
            theme_msg,
            call.message.chat.id,
            call.message.message_id,
            parse_mode="Markdown"
        )
    except:
        bot.send_message(call.message.chat.id, theme_msg, parse_mode="Markdown")
    
    bot.send_chat_action(call.message.chat.id, 'typing')
    
    # Get REAL products
    products = scraper.get_real_products(theme_key, limit=4)
    
    if products:
        # Send header
        header = (
            f"✅ *REAL PRODUCTS FOUND!*\n"
            f"*Style:* {theme_data['display_name']}\n"
            f"*Source:* Zalora Malaysia\n"
            f"━━━━━━━━━━━━━━━━━━━━━━━"
        )
        
        bot.send_message(call.message.chat.id, header, parse_mode="Markdown")
        
        # Send each product
        for i, product in enumerate(products, 1):
            caption = (
                f"📦 *{product['name']}*\n"
                f"💰 *Price:* {product['price']}\n"
                f"🎯 *Style:* {theme_data['display_name']}\n"
                f"🛍️ *Source:* Zalora Malaysia\n"
                f"📊 *Item {i}/{len(products)}*"
            )
            
            markup = types.InlineKeyboardMarkup()
            markup.row(
                types.InlineKeyboardButton("🛍️ VIEW & BUY NOW", url=product['link'])
            )
            
            # Try to send image
            if product.get('image') and product['image'].startswith('http'):
                try:
                    bot.send_photo(
                        call.message.chat.id,
                        product['image'],
                        caption=caption,
                        parse_mode="Markdown",
                        reply_markup=markup
                    )
                except:
                    bot.send_message(
                        call.message.chat.id,
                        caption,
                        parse_mode="Markdown",
                        reply_markup=markup
                    )
            else:
                bot.send_message(
                    call.message.chat.id,
                    caption,
                    parse_mode="Markdown",
                    reply_markup=markup
                )
            
            time.sleep(0.5)
        
        # Summary
        summary = (
            f"🎉 *{len(products)} REAL PRODUCTS!*\n\n"
            f"*What's next:*\n"
            f"1. Click 🛍️ buttons to view products\n"
            f"2. Check details on Zalora\n"
            f"3. Add to cart to purchase\n\n"
            f"*Want more? Try another theme!*"
        )
        
        markup = types.InlineKeyboardMarkup(row_width=2)
        markup.add(
            types.InlineKeyboardButton("🔄 Another Theme", callback_data="browse_themes"),
            types.InlineKeyboardButton("✨ Recommendation", callback_data="get_recommendation")
        )
        
        bot.send_message(
            call.message.chat.id,
            summary,
            parse_mode="Markdown",
            reply_markup=markup
        )
        
    else:
        # If no products found, provide direct search link
        keywords = "+".join(theme_data['zalora_keywords'][:2])
        search_url = f"https://www.zalora.com.my/catalog/?q={keywords}"
        
        fallback_msg = (
            f"🔍 *Browse {theme_data['display_name']} on Zalora*\n\n"
            f"*For the best selection, visit Zalora directly:*\n\n"
            f"Click below to see ALL available products!"
        )
        
        markup = types.InlineKeyboardMarkup()
        markup.row(
            types.InlineKeyboardButton(f"🔍 Browse on Zalora", url=search_url)
        )
        markup.row(
            types.InlineKeyboardButton("👕 Try Another Theme", callback_data="browse_themes"),
            types.InlineKeyboardButton("🏠 Main Menu", callback_data="main_menu")
        )
        
        bot.send_message(
            call.message.chat.id,
            fallback_msg,
            parse_mode="Markdown",
            reply_markup=markup
        )

@bot.callback_query_handler(func=lambda call: call.data == "direct_zalora")
def handle_direct_zalora(call):
    """Go directly to Zalora"""
    markup = types.InlineKeyboardMarkup()
    markup.row(
        types.InlineKeyboardButton("🛍️ VISIT ZALORA MALAYSIA", url="https://www.zalora.com.my")
    )
    markup.row(
        types.InlineKeyboardButton("👕 Browse Themes", callback_data="browse_themes")
    )
    
    bot.send_message(
        call.message.chat.id,
        "🛍️ *Zalora Malaysia - Direct Access*\n\n"
        "Click below to visit Zalora Malaysia directly:\n"
        "• Browse thousands of products\n"
        "• All categories available\n"
        "• Secure shopping\n"
        "• Fast delivery",
        parse_mode="Markdown",
        reply_markup=markup
    )

@bot.callback_query_handler(func=lambda call: call.data == "browse_themes")
def handle_browse_themes(call):
    """Go back to theme selection"""
    send_all_themes(call.message.chat.id)

@bot.message_handler(func=lambda message: message.text == "🎨 BROWSE ALL THEMES")
def handle_browse_all_themes(message):
    send_all_themes(message.chat.id)

@bot.message_handler(func=lambda message: message.text == "✨ GET RECOMMENDATION")
def handle_get_recommendation(message):
    """Get random recommendation"""
    theme_key = random.choice(list(THEMES.keys()))
    theme_data = THEMES[theme_key]
    occasion = random.choice(theme_data["occasions"])
    
    recommendation = (
        f"✨ *PERSONALIZED RECOMMENDATION*\n\n"
        f"🎯 *For:* {occasion}\n"
        f"👗 *Style:* {theme_data['display_name']}\n"
        f"📝 *Description:* {theme_data['description']}\n\n"
        f"*Searching Zalora for REAL products...*"
    )
    
    bot.send_message(message.chat.id, recommendation, parse_mode="Markdown")
    
    # Get products
    products = scraper.get_real_products(theme_key, limit=3)
    
    if products:
        for product in products:
            caption = (
                f"📦 *{product['name']}*\n"
                f"💰 {product['price']}\n"
                f"🎯 {theme_data['display_name']}"
            )
            
            markup = types.InlineKeyboardMarkup()
            markup.row(
                types.InlineKeyboardButton("🛍️ View on Zalora", url=product['link'])
            )
            
            if product.get('image') and product['image'].startswith('http'):
                try:
                    bot.send_photo(message.chat.id, product['image'], caption=caption, 
                                 parse_mode="Markdown", reply_markup=markup)
                except:
                    bot.send_message(message.chat.id, caption, parse_mode="Markdown", reply_markup=markup)
            else:
                bot.send_message(message.chat.id, caption, parse_mode="Markdown", reply_markup=markup)
    else:
        # Direct search link
        keywords = "+".join(theme_data['zalora_keywords'][:2])
        search_url = f"https://www.zalora.com.my/catalog/?q={keywords}"
        
        markup = types.InlineKeyboardMarkup()
        markup.row(
            types.InlineKeyboardButton(f"🔍 Browse {theme_data['display_name']}", url=search_url)
        )
        
        bot.send_message(
            message.chat.id,
            f"🎯 *{theme_data['display_name']} for {occasion}*\n\n"
            f"Click to browse products on Zalora:",
            parse_mode="Markdown",
            reply_markup=markup
        )

@bot.message_handler(func=lambda message: message.text == "🔍 SEARCH PRODUCTS")
def handle_search_products(message):
    msg = bot.send_message(
        message.chat.id,
        "🔍 *Search Zalora Malaysia*\n\n"
        "What are you looking for?\n"
        "Examples: 'black dress', 'running shoes', 'jeans'\n\n"
        "Type your search:",
        parse_mode="Markdown"
    )
    bot.register_next_step_handler(msg, process_search)

def process_search(message):
    query = message.text.strip()
    
    if not query or len(query) < 2:
        bot.send_message(message.chat.id, "❌ Please enter at least 2 characters")
        return
    
    bot.send_message(
        message.chat.id,
        f"🔍 *Searching:* `{query}`\n\nPlease wait...",
        parse_mode="Markdown"
    )
    
    # Create direct search link
    encoded_query = quote(query)
    search_url = f"https://www.zalora.com.my/catalog/?q={encoded_query}"
    
    # Try to get products
    try:
        products = scraper._search_zalora(query, limit=4)
        
        if products:
            bot.send_message(
                message.chat.id,
                f"✅ *Found {len(products)} products*",
                parse_mode="Markdown"
            )
            
            for product in products:
                caption = f"📦 *{product['name']}*\n💰 {product['price']}"
                markup = types.InlineKeyboardMarkup()
                markup.row(
                    types.InlineKeyboardButton("🛍️ View on Zalora", url=product['link'])
                )
                
                if product.get('image') and product['image'].startswith('http'):
                    try:
                        bot.send_photo(message.chat.id, product['image'], caption=caption, 
                                     parse_mode="Markdown", reply_markup=markup)
                    except:
                        bot.send_message(message.chat.id, caption, parse_mode="Markdown", reply_markup=markup)
                else:
                    bot.send_message(message.chat.id, caption, parse_mode="Markdown", reply_markup=markup)
        else:
            # Provide direct search link
            markup = types.InlineKeyboardMarkup()
            markup.row(
                types.InlineKeyboardButton("🔍 Search on Zalora", url=search_url)
            )
            
            bot.send_message(
                message.chat.id,
                f"🔗 *Direct Search Link*\n\n"
                f"Click to search '{query}' on Zalora Malaysia:",
                parse_mode="Markdown",
                reply_markup=markup
            )
            
    except Exception as e:
        logger.error(f"Search error: {e}")
        # Fallback to direct link
        markup = types.InlineKeyboardMarkup()
        markup.row(
            types.InlineKeyboardButton("🔍 Search on Zalora", url=search_url)
        )
        
        bot.send_message(
            message.chat.id,
            f"🔗 *Direct Search Link*\n\n"
            f"Click to search '{query}' on Zalora:",
            parse_mode="Markdown",
            reply_markup=markup
        )

@bot.message_handler(func=lambda message: message.text == "📋 HOW TO USE")
def handle_how_to_use(message):
    """Show how to use"""
    guide = """
    📋 *HOW TO USE - REAL PRODUCTS*
    
    *🎯 GETTING REAL PRODUCTS:*
    
    1. *CHOOSE A THEME*
       • Select from fashion styles
       • Bot searches Zalora LIVE
       • Shows actual products
    
    2. *VIEW PRODUCTS* 
       • Real names and prices
       • Direct Zalora links
       • Product images
    
    3. *SHOP DIRECTLY*
       • Click "VIEW & BUY NOW"
       • Goes to Zalora product page
       • Add to cart and purchase
    
    *✨ TIPS FOR BEST RESULTS:*
    • Good internet connection
    • Be patient - searching takes time
    • Products are loaded from Zalora
    • Check size charts on Zalora
    
    *🛍️ ABOUT ZALORA:*
    • Malaysia's top online fashion store
    • Thousands of brands
    • Secure payment options
    
    *Ready to find REAL fashion?* 👇
    """
    
    markup = types.InlineKeyboardMarkup()
    markup.row(
        types.InlineKeyboardButton("🎨 BROWSE THEMES", callback_data="browse_themes"),
        types.InlineKeyboardButton("✨ GET RECOMMENDATION", callback_data="get_recommendation")
    )
    
    bot.send_message(
        message.chat.id,
        guide,
        parse_mode="Markdown",
        reply_markup=markup
    )

@bot.callback_query_handler(func=lambda call: call.data in ["main_menu", "get_recommendation"])
def handle_callbacks(call):
    if call.data == "main_menu":
        send_welcome(call.message)
    elif call.data == "get_recommendation":
        handle_get_recommendation(call.message)

# ========== START BOT ==========
print("\n" + "="*60)
print("✅ REAL PRODUCT BOT - SIMPLE VERSION")
print("="*60)
print("🎯 Features:")
print("• REAL Zalora products")
print("• No Selenium required")
print("• Simple installation")
print("• Direct shopping links")
print(f"• {len(THEMES)} fashion themes")
print("\n📦 Installation:")
print("1. Run: pip install telebot requests beautifulsoup4")
print("2. Run this script")
print("3. Enter your bot token")
print("\n🚀 Starting bot...")

try:
    bot_info = bot.get_me()
    print(f"✅ Bot: @{bot_info.username}")
    print(f"🔗 Link: https://t.me/{bot_info.username}")
    print("\n" + "="*60)
    print("🤖 Bot is running! Users will get REAL Zalora products.")
    print("="*60)
    
    bot.polling(none_stop=True)
    
except Exception as e:
    print(f"❌ Bot error: {e}")
    input("Press Enter to exit...")