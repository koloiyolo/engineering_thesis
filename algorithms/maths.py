import numpy as np
import math


def euclidean_distance(point1, point2):
    sums = 0
    for val1, val2 in zip(point1, point2):
        dif = val1 - val2
        sums += dif * dif
    return np.sqrt(sums)


def getmin(data):
    min = list(data[0])
    for elem in data:
        for i, value in enumerate(elem):
            if min[i] > value:
                min[i] = math.floor(elem[i])
    return min


def getmax(data):
    max = list(data[0])
    for elem in data:
        for i, value in enumerate(elem):
            if max[i] < value:
                max[i] = math.floor(elem[i])
    return max


def means(data):
    sums = 0
    count = 0
    for elem in data:
        count = count + 1
        sums += elem

    return sums / count
