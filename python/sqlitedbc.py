import sqlite3

class SqliteDBC:

	def __init__(self, database):
		self.conn = sqlite3.connect( database )
		self.table_name = ""
		self.columns = ""
		self.where = ""
		self.where_dict = {}
		self.limit_count = 0
		self.order_by = ""
		self.order_type = "ASC"
		self.sql = ""
		


	def table(self, table_name = ""):
		self.table_name = table_name
		return self


	def insert(self, values = {}):
		placeholders = [':'+key for key in values.keys()]
		sql = "INSERT INTO {} ({}) VALUES({})".format(self.table_name, ','.join(values.keys()), ','.join(placeholders))
		
		try:
			cursor = self.conn.cursor()
			cursor.execute(sql, values)
			last_row_id = cursor.lastrowid 
			self.conn.commit()
			cursor.close()
		except Exception as e:
			raise e
			print("Insert statement failed.")
		
		return last_row_id

	def select(self, columns = []):
		if len(columns) == 0:
			self.columns = "*"
		else:
			for c in columns:
				self.columns = ','.join(columns)

		return self

	def eq(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} = :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("eq statement failed.")
		 

	def neq(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} != :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("neq statement failed.")

	def gt(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} > :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("gt statement failed.")

	def gte(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} >= :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("gte statement failed.")

	def lt(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} < :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("lt statement failed.")

	def lte(self, key_value = {}):
		try:
			key = key_value.keys()[0]
			self.where_dict[key] = key_value[key]
			self.where = self.where + " {} <= :{} ".format(key, key)
			return self
		except Exception as e:
			raise e
			print("lte statement failed.")

	def andop(self):
		if self.where != "":
			self.where = self.where + " AND "
		else:
			print("AND operator failed")

		return self

	def orop(self):
		if self.where != "":
			self.where = self.where + " OR "
		else:
			print("OR operator failed")

		return self 

	def limit(self, limit_count):
		if int(limit_count) > 0:
			self.limit_count = limit_count

		return self

	def orderby(self, order_by, order_type = "ASC"):
		if order_type.upper() in ["ASC", "DESC"]:
			self.order_type = order_type.upper()
			self.order_by = order_by

		return self

	def fetch(self):
		sql = "SELECT {} FROM {} ".format(self.columns, self.table_name)
		if self.where != "":
			sql = sql + " WHERE {}".format(self.where)

		if self.order_by != "":
			sql = sql + " ORDER BY {} {}".format(self.order_by, self.order_type)

		if int(self.limit_count) > 0:
			sql = sql + " LIMIT {}".format(self.limit_count)

		
		try:
			cursor = self.conn.cursor()
			cursor.execute(sql, self.where_dict)
			if self.limit_count == 1:
				result = cursor.fetchone()
			else:
				result = cursor.fetchall()

		except Exception as e:
			raise e

		self.table_name = ""
		self.columns = ""
		self.where = ""
		self.where_dict = {}
		self.limit_count = 0
		self.order_by = ""
		self.order_type = "ASC"

		return result


	def update(self):
		pass

	def delete(self):
		pass


	def execute(self, sql):
		pass