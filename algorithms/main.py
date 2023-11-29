import json

from flask import Flask, request, jsonify
import kmeans

app = Flask(__name__)


@app.route('/kmeans', methods=['POST'])
def kmeans_route():
    if request.method == 'POST':
        try:
            centroids = int(request.form.get("centroids"))
            iters = int(request.form.get('iterations'))
            data = json.loads(request.form.get('data'))
            print(centroids)
            print(data)
            # if not isinstance(data, list) or any(not isinstance(pair, list) or len(pair) != 2 or any(not isinstance(coord, float) for coord in pair) for pair in data):
            #     raise ValueError('Invalid format for data. Expecting a list of integer pairs.')

        except (ValueError, json.JSONDecodeError) as e:
            return jsonify({'error': f'Invalid data format: {str(e)}'}), 400

        return jsonify('clusters', kmeans.kmeans(data, centroids, iters))


if __name__ == '__main__':
    app.run(host='0.0.0.0', debug=True)