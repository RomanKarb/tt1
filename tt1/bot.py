import telebot
import os

bot = telebot.TeleBot('6117896964:AAGNr0UHD4gaCKk9OWN_SN4krj2bKJmXR7M')

keyboard = telebot.types.ReplyKeyboardMarkup(row_width=1, resize_keyboard=True)
contact_button = telebot.types.KeyboardButton(text="Отправить контакт", request_contact=True)
keyboard.add(contact_button)

users = {}

@bot.message_handler(commands=['start'])
def start(message):
    try:
        code = message.text.split()[1]
        users[message.chat.id] = code
        bot.send_message(message.chat.id, 'Пожалуйста, отправьте свой номер телефона', reply_markup=telebot.types.ReplyKeyboardRemove())
    except:
        bot.send_message(message.chat.id, 'Вы должны перейти в бот через сайт а не нажать (написать) /start')

@bot.message_handler(func=lambda message: message.chat.id in users and message.content_type == 'text')
def receive_contact(message):
    code = users[message.chat.id]
    phone_number = message.text.replace('+', '').replace('-', '').replace('(', '').replace(')', '').replace(' ', '').replace('=', '')
    if phone_number.isdigit():
        with open(f'codes_safe/{code}.verify', 'w') as f:
            f.write(f'code={code};c phone={phone_number};p chat.id={message.chat.id};c.i\n')
            bot.send_message(message.chat.id, 'Спасибо, вы авторизованы. Пожалуйста нажмите "Проверить" на сайте')
        del users[message.chat.id]
    else:
        bot.send_message(message.chat.id, 'Неизвестная команда')

@bot.message_handler(func=lambda message: message.chat.id in users and message.content_type == 'contact')
def receive_text(message):
    bot.send_message(message.chat.id, 'Пожалуйста, отправьте свой номер телефона')

@bot.message_handler(func=lambda message: message.chat.id in users and message.content_type != 'contact' and message.content_type != 'text')
def unknown_command(message):
    bot.send_message(message.chat.id, 'Неизвестная команда')

@bot.message_handler(func=lambda message: message.content_type == 'text')
def check_command(message):
    text = message.text.lower()
    if text.startswith('/start') or text.isdigit():
        pass #do nothing
    else:
        bot.send_message(message.chat.id, 'Неизвестная команда')

bot.polling()
