import json
from flask import Flask, request, jsonify

import kmeans
import ahc

app = Flask(__name__)
kmeans = kmeans.Kmeans()


@app.route('/kmeans', methods=['POST'])
def kmeans_route():
    if request.method == 'POST':
        try:
            centroids = int(request.form.get("centroids"))
            iterations = int(request.form.get('iterations'))
            data = json.loads(request.form.get('data'))

        except (ValueError, json.JSONDecodeError) as e:
            return jsonify({'error': f'Invalid data format: {str(e)}'}), 400

        return jsonify(kmeans.exec(data, centroids, iterations))



@app.route('/ahc', methods=['POST'])
def ahc_route():
    if request.method == 'POST':
        try:
            clusters = int(request.form.get('clusters'))
            data = json.loads(request.form.get('data'))

        except (ValueError, json.JSONDecodeError) as e:
            return jsonify({'error': f'Invalid data format: {str(e)}'}), 400

        return jsonify('clusters', ahc.ahc(data, clusters))



if __name__ == '__main__':
    app.run(host='0.0.0.0', debug=True)