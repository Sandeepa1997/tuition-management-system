import sys
from textblob import TextBlob 

feedback = sys.argv[1]

blob=TextBlob(feedback)

polarity=blob.sentiment.polarity

if polarity>0:
    print("Positive")
elif polarity<0:
    print("Negative")
else:
    print("Neutral")