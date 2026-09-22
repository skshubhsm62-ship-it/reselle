import os
import telebot
import requests
from telebot import types

BOT_TOKEN = os.environ.get('8751093168:AAEI8xh7DI_h0Ty20o_KB9wsa_9MjYKb6qE')
ADMIN_ID = int(os.environ.get('8970732487', '0'))
API_URL = os.environ.get('API_URL', 'https://hunter.site.je/api.php')
API_SECRET = os.environ.get('HUNTER', '')

bot = telebot.TeleBot(BOT_TOKEN)

# ============== HELPERS ==============

def api(action, **kwargs):
    params = {'secret': API_SECRET, 'action': action}
    params.update(kwargs)
    try:
        r = requests.get(API_URL, params=params, timeout=10)
        return r.json()
    except Exception as e:
        return {'error': str(e)}

def time_remaining(expires_at):
    if not expires_at:
        return "Lifetime"
    from datetime import datetime
    try:
        exp = datetime.strptime(str(expires_at), '%Y-%m-%d %H:%M:%S')
        now = datetime.now()
        diff = exp - now
        if diff.total_seconds() <= 0:
            return "EXPIRED"
        days = diff.days
        hours = diff.seconds // 3600
        mins = (diff.seconds % 3600) // 60
        if days > 0:
            return f"{days}d {hours}h left"
        elif hours > 0:
            return f"{hours}h {mins}m left"
        else:
            return f"{mins}m left"
    except:
        return str(expires_at)

# ============== COMMANDS ==============

@bot.message_handler(commands=['start'])
def start(message):
    text = (
        "🎮 *Welcome to HUNTER MODS Store!*\n\n"
        "Commands:\n"
        "• /products - Products dekho\n"
        "• /mykey - Apni keys dekho\n"
        "• /link - Website account link karo\n"
        "• /wallet - Wallet balance\n"
        "• /help - Help\n\n"
        "🌐 Website: https://hunter.site.je"
    )
    bot.reply_to(message, text, parse_mode='Markdown')

@bot.message_handler(commands=['help'])
def help_cmd(message):
    text = (
        "📖 *Help*\n\n"
        "*/products* - Available products + plans\n"
        "*/mykey* - Apni purchased keys + expiry\n"
        "*/link CODE* - Website se link karo\n"
        "*/wallet* - Wallet balance check\n"
        "*/support* - Contact admin"
    )
    bot.reply_to(message, text, parse_mode='Markdown')

@bot.message_handler(commands=['products'])
def products(message):
    res = api('products')
    if 'products' not in res or not res['products']:
        bot.reply_to(message, "❌ Koi product available nahi hai.")
        return

    text = "📦 *Available Products:*\n\n"
    for p in res['products']:
        text += f"• *{p['name']}*\n"
        text += f"  💰 Rs.{p['price']} | Stock: {p['stock']}\n\n"
    text += "🛒 Buy karne ke liye website visit karo:\nhttps://hunter.site.je"
    bot.reply_to(message, text, parse_mode='Markdown')

@bot.message_handler(commands=['link'])
def link(message):
    parts = message.text.split()
    if len(parts) < 2:
        bot.reply_to(message,
            "❌ *Usage:* `/link CODE`\n\n"
            "Website pe login karo → *Profile* page → *Generate Link Code* → phir wo code yahan bhejo.",
            parse_mode='Markdown')
        return

    code = parts[1].strip().upper()
    tg_id = message.from_user.id
    tg_username = message.from_user.username or ''

    res = api('link', telegram_id=tg_id, telegram_username=tg_username, code=code)

    if res.get('success'):
        bot.reply_to(message,
            f"✅ Successfully linked with website account: *{res['username']}*\n\n"
            f"Ab `/mykey` se apni keys dekh sakte ho.",
            parse_mode='Markdown')
    else:
        bot.reply_to(message,
            f"❌ Link failed: {res.get('error', 'Unknown error')}\n\n"
            f"Code galat hai ya expire ho gaya.",
            parse_mode='Markdown')

@bot.message_handler(commands=['mykey'])
def mykey(message):
    tg_id = message.from_user.id
    res = api('my_keys', telegram_id=tg_id)

    if 'keys' not in res or not res['keys']:
        bot.reply_to(message,
            "❌ Koi key nahi mili.\n\n"
            "Pehle website pe purchase karo, phir `/link` se account link karo.",
            parse_mode='Markdown')
        return

    text = "🔑 *Your Keys:*\n\n"
    for k in res['keys']:
        text += f"📦 *{k['product_name']}*\n"
        text += f"`{k['key']}`\n"
        text += f"⏱️ {time_remaining(k.get('expires_at'))}\n\n"

    bot.reply_to(message, text, parse_mode='Markdown')

@bot.message_handler(commands=['wallet'])
def wallet(message):
    tg_id = message.from_user.id
    res = api('get_user', telegram_id=tg_id)

    if not res.get('user'):
        bot.reply_to(message,
            "❌ Account link nahi hai.\n\n"
            "Pehle `/link CODE` se apna website account link karo.",
            parse_mode='Markdown')
        return

    u = res['user']
    text = (
        f"👤 *{u['username']}*\n"
        f"💰 Wallet: Rs.{u['wallet']}"
    )
    bot.reply_to(message, text, parse_mode='Markdown')

@bot.message_handler(commands=['support'])
def support(message):
    bot.reply_to(message, "📞 Contact admin: @HAPPYXLIVE")

# ============== START BOT ==============

if __name__ == '__main__':
    print("Bot started...")
    bot.infinity_polling(timeout=30, long_polling_timeout=25)
