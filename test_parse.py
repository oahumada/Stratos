import json

with open('test_response.json', 'r') as f:
    text = f.read()

text = text.strip()
if text.startswith('```json'):
    text = text[7:]
elif text.startswith('```'):
    text = text[3:]
if text.endswith('```'):
    text = text[:-3]
text = text.strip()

try:
    data = json.loads(text)
    print("SUCCESS")
except Exception as e:
    print("FAILED:", e)
    print("END OF TEXT:")
    print(repr(text[-100:]))
