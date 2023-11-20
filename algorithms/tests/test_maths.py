import unittest
import maths


class TestMaths(unittest.TestCase):

    def test_euclidean(self):
        self.assertEqual(maths.euclidean_distance([1, 2], [4, 6]), 5)

    def test_x_y_min(self):
        data = [[0, 1], [-2, 5], [-3, 2], [5, 12]]
        self.assertEqual(maths.getmin(data), [-3, 1])

    def test_x_y_max(self):
        data = [[0, 1], [-2, 5], [-3, 2], [-2, 12], [5, -3]]
        self.assertEqual(maths.getmax(data), [5, 12])

    def test_means(self):
        data = [5, 1, 6, 7, 9, 2]
        self.assertEqual(5, maths.means(data))
        self.assertEqual(0, maths.means([2, -2]))
