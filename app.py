
import os, sqlite3, joblib, pandas as pd
from flask import Flask, render_template, request, jsonify
from sklearn.model_selection import train_test_split
from sklearn.neighbors import KNeighborsClassifier
from sklearn.metrics import accuracy_score, confusion_matrix

BASE = os.path.dirname(os.path.abspath(__file__))
DB = os.path.join(BASE, "students.db")
DATA = os.path.join(BASE, "data", "students.csv")
MODEL = os.path.join(BASE, "models", "student_success_knn.joblib")

app = Flask(__name__)

def init_db():
    con=sqlite3.connect(DB)
    con.execute("""CREATE TABLE IF NOT EXISTS students(
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        attendance REAL NOT NULL,
        grades REAL NOT NULL,
        assignments REAL NOT NULL,
        participation REAL NOT NULL,
        prediction INTEGER NOT NULL,
        probability REAL NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )""")
    con.commit(); con.close()

def train_model():
    df=pd.read_csv(DATA)
    X=df[["attendance","grades","assignments","participation"]]
    y=df["success"]
    Xtr,Xte,ytr,yte=train_test_split(X,y,test_size=.2,random_state=42,stratify=y)
    model=KNeighborsClassifier(n_neighbors=7, weights="distance")
    model.fit(Xtr,ytr)
    pred=model.predict(Xte)
    acc=accuracy_score(yte,pred)
    cm=confusion_matrix(yte,pred).tolist()
    joblib.dump({"model":model,"accuracy":acc,"confusion_matrix":cm},MODEL)
    return acc,cm

init_db()
if not os.path.exists(MODEL):
    train_model()
bundle=joblib.load(MODEL)
model=bundle["model"]

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/predict", methods=["POST"])
def predict():
    d=request.get_json()
    try:
        vals=[float(d[k]) for k in ["attendance","grades","assignments","participation"]]
        if any(v<0 or v>100 for v in vals): raise ValueError
        x=pd.DataFrame([vals],columns=["attendance","grades","assignments","participation"])
        p=int(model.predict(x)[0])
        probs=model.predict_proba(x)[0]
        prob=float(probs[list(model.classes_).index(p)])*100
        return jsonify({"success":True,"prediction":"ناجح" if p else "راسب","probability":round(prob,2)})
    except Exception:
        return jsonify({"success":False,"message":"تأكد من إدخال قيم بين 0 و100."}),400

@app.route("/save", methods=["POST"])
def save():
    d=request.get_json()
    result=__import__("requests") if False else None
    vals=[float(d[k]) for k in ["attendance","grades","assignments","participation"]]
    x=pd.DataFrame([vals],columns=["attendance","grades","assignments","participation"])
    p=int(model.predict(x)[0]); probs=model.predict_proba(x)[0]
    prob=float(probs[list(model.classes_).index(p)])*100
    con=sqlite3.connect(DB)
    con.execute("""INSERT INTO students(name,attendance,grades,assignments,participation,prediction,probability)
                   VALUES(?,?,?,?,?,?,?)""",
                (d["name"],*vals,p,prob))
    con.commit(); con.close()
    return jsonify({"success":True})

@app.route("/stats")
def stats():
    con=sqlite3.connect(DB)
    df=pd.read_sql_query("SELECT * FROM students",con)
    con.close()
    if df.empty:
        saved={"total":0,"passed":0,"failed":0,"pass_rate":0,"fail_rate":0}
    else:
        total=len(df); passed=int(df.prediction.sum()); failed=total-passed
        saved={"total":total,"passed":passed,"failed":failed,
               "pass_rate":round(passed/total*100,2),"fail_rate":round(failed/total*100,2)}
    source=pd.read_csv(DATA)
    return jsonify({
        "saved":saved,
        "dataset":{"total":len(source),"passed":int(source.success.sum()),
                   "failed":int((source.success==0).sum()),
                   "pass_rate":round(source.success.mean()*100,2)},
        "averages":{k:round(float(source[k].mean()),2) for k in ["attendance","grades","assignments","participation"]},
        "accuracy":round(bundle["accuracy"]*100,2),
        "confusion_matrix":bundle["confusion_matrix"]
    })

@app.route("/students")
def students():
    con=sqlite3.connect(DB)
    df=pd.read_sql_query("SELECT id,name,attendance,grades,assignments,participation,prediction,probability,created_at FROM students ORDER BY id DESC",con)
    con.close()
    return jsonify(df.to_dict(orient="records"))

if __name__=="__main__":
    app.run(debug=True)
