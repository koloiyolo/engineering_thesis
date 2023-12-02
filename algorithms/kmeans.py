from random import randrange

import maths


def kmeans(data, centroid_count, iterations=100):

    data_with_centroids = []
    centroids = []
    clusters = []

    # set initial centroid positions
    for _ in range(centroid_count):
        centroids.append([(randrange(0, 1000) / 1000) for i in range(len(list(data[0])))])

    for _ in range(iterations):
        data_with_centroids.clear()

        # assign each data point its closest centroid
        for point in data:
            current_centroid = centroids[0]
            for centroid in centroids:
                if maths.euclidean_distance(point, current_centroid) > maths.euclidean_distance(point, centroid):
                    current_centroid = centroid
            data_with_centroids.append([point, current_centroid])

        # move centroids to more accurate positions
        for centroid in centroids:
            count = 0
            points = []
            scaling_factor = 2 * len(data)

            for point in data_with_centroids:
                if point[1] == centroid:
                    count += 1
                    points.append(point[0])

            for point in points:
                for j in range(len(centroid)):
                    centroid[j] += round(maths.means([point[j], centroid[j]]) / scaling_factor)

    # reassign data to closest centroids
    data_with_centroids.clear()
    for point in data:
        current_centroid = centroids[0]
        for centroid in centroids:
            if maths.euclidean_distance(point, current_centroid) > maths.euclidean_distance(point, centroid):
                current_centroid = centroid
        data_with_centroids.append([point, current_centroid])

    # assign data to clusters based on their current centroid
    for centroid in centroids:
        cluster = []
        for point in data_with_centroids:
            if centroid == point[1]:
                cluster.append(point[0])
        clusters.append(cluster)


    return clusters
