import json

with open('raw_11.txt', 'r') as f:
    text = f.read().strip()

if text.startswith('```json'):
    text = text[7:]
elif text.startswith('```'):
    text = text[3:]
if text.endswith('```'):
    text = text[:-3]
text = text.strip()

try:
    data = json.loads(text)
    print("SUCCESS: JSON is valid")
except Exception as e:
    print("FAILED:", e)
    print("TRUNCATED ENDING?")
    print(repr(text[-100:]))
